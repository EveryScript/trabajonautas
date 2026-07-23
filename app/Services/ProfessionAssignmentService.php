<?php

namespace App\Services;

use App\Models\Area;
use App\Models\BotVacancyPreview;
use App\Models\Profesion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfessionAssignmentService
{
    public function catalog(): array
    {
        return Area::query()
            ->orderBy('id')
            ->get(['id', 'area_name'])
            ->map(fn (Area $area): array => [
                'id' => (int) $area->id,
                'name' => $area->area_name,
            ])
            ->all();
    }

    public function resolve(array $requestedAreaIds): array
    {
        $invalidValues = [];
        $areaIds = collect($requestedAreaIds)
            ->map(function (mixed $value) use (&$invalidValues): ?int {
                if (is_int($value) && $value > 0) {
                    return $value;
                }

                if (is_string($value) && ctype_digit(trim($value)) && (int) $value > 0) {
                    return (int) $value;
                }

                $invalidValues[] = is_scalar($value) ? (string) $value : gettype($value);

                return null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $areas = Area::query()
            ->whereIn('id', $areaIds)
            ->orderBy('id')
            ->get(['id', 'area_name']);
        $resolvedAreaIds = $areas->pluck('id')->map(fn ($id): int => (int) $id)->values();
        $missingAreaIds = $areaIds->diff($resolvedAreaIds)->values()->all();
        $valid = $areaIds->isNotEmpty() && $invalidValues === [] && $missingAreaIds === [];

        $professionIds = $valid ? $this->professionIdsForAreaIds($resolvedAreaIds->all()) : [];
        $professions = $professionIds
            ? Profesion::query()->whereIn('id', $professionIds)->orderBy('id')->get(['id', 'profesion_name'])
            : collect();

        return [
            'valid' => $valid && $professionIds !== [],
            'requested_area_ids' => $areaIds->all(),
            'area_ids' => $resolvedAreaIds->all(),
            'area_names' => $areas->pluck('area_name')->values()->all(),
            'invalid_area_ids' => $missingAreaIds,
            'invalid_area_values' => array_values(array_unique($invalidValues)),
            'profession_ids' => $professions->pluck('id')->map(fn ($id): int => (int) $id)->values()->all(),
            'profession_names' => $professions->pluck('profesion_name')->values()->all(),
            'error' => $this->resolutionError($areaIds->all(), $missingAreaIds, $invalidValues, $professionIds),
        ];
    }

    public function resolveExactAreaNames(array $areaNames): array
    {
        $names = collect($areaNames)
            ->filter(fn (mixed $name): bool => is_string($name) && trim($name) !== '')
            ->map(fn (string $name): string => trim($name))
            ->unique(fn (string $name): string => $this->normalize($name))
            ->values();
        $catalog = Area::query()
            ->get(['id', 'area_name'])
            ->keyBy(fn (Area $area): string => $this->normalize($area->area_name));
        $resolvedIds = [];
        $unmatched = [];

        foreach ($names as $name) {
            $area = $catalog->get($this->normalize($name));

            if ($area) {
                $resolvedIds[] = (int) $area->id;
            } else {
                $unmatched[] = $name;
            }
        }

        $assignment = $this->resolve($resolvedIds);

        if ($unmatched !== []) {
            $assignment['valid'] = false;
            $assignment['profession_ids'] = [];
            $assignment['profession_names'] = [];
            $assignment['invalid_area_values'] = $unmatched;
            $assignment['error'] = 'Se recibieron areas que no coinciden exactamente con el catalogo.';
        }

        return $assignment;
    }

    public function professionIdsForAreaIds(array $areaIds): array
    {
        $areaIds = collect($areaIds)
            ->map(fn (mixed $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($areaIds === []) {
            return [];
        }

        return Profesion::query()
            ->whereHas('areas', function ($query) use ($areaIds): void {
                $query->whereIn('areas.id', $areaIds);
            })
            ->distinct()
            ->orderBy('profesions.id')
            ->pluck('profesions.id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    public function applyToPreview(BotVacancyPreview $preview, array $assignment, array $context = []): array
    {
        $before = collect($preview->selected_profession_ids ?: [])
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
        $manual = $this->professionsEditedManually($preview);
        $after = $manual ? $before : array_values($assignment['profession_ids'] ?? []);

        if (! $manual) {
            $valid = (bool) ($assignment['valid'] ?? false);
            $rawData = array_merge($preview->raw_data ?? [], [
                'resolved_area_ids' => array_values($assignment['area_ids'] ?? []),
                'profession_assignment_source' => 'area_profession_pivot',
                'profession_assignment_error' => $assignment['error'] ?? null,
                'profession_assignment_updated_at' => now()->toIso8601String(),
            ]);

            $preview->update([
                'area' => $valid ? implode(', ', $assignment['area_names'] ?? []) : 'No especificado',
                'professions' => $valid ? implode(', ', $assignment['profession_names'] ?? []) : 'No especificado',
                'selected_area_id' => $valid ? ($assignment['area_ids'][0] ?? null) : null,
                'selected_profession_ids' => $after,
                'status' => ! $valid && in_array($preview->status, ['preview', 'error'], true)
                    ? 'error'
                    : $preview->status,
                'raw_data' => $rawData,
            ]);
        }

        $this->logDecision(array_merge($context, [
            'preview_id' => $preview->id,
            'scrape_batch_id' => $preview->scrape_batch_id,
            'resolved_area_ids' => $assignment['area_ids'] ?? [],
            'professions_before' => $before,
            'professions_from_areas' => $assignment['profession_ids'] ?? [],
            'professions_from_ai' => [],
            'professions_after' => $after,
            'professions_edited_manually' => $manual,
        ]));

        return [
            ...$assignment,
            'professions_before' => $before,
            'professions_after' => $after,
            'preserved_manual_selection' => $manual,
        ];
    }

    public function professionsEditedManually(BotVacancyPreview $preview): bool
    {
        return (bool) data_get($preview->raw_data, 'manual_professions_locked')
            || ($preview->status === 'edited' && $preview->selected_profession_ids !== null);
    }

    public function logDecision(array $context): void
    {
        Log::debug('BOT area-profession assignment.', [
            'raw_ai_areas' => $context['raw_ai_areas'] ?? [],
            'raw_ai_professions' => $context['raw_ai_professions'] ?? [],
            'resolved_area_ids' => $context['resolved_area_ids'] ?? [],
            'professions_before' => $context['professions_before'] ?? [],
            'professions_from_areas' => $context['professions_from_areas'] ?? [],
            'professions_from_ai' => [],
            'professions_after' => $context['professions_after'] ?? [],
            'preview_id' => $context['preview_id'] ?? null,
            'scrape_batch_id' => $context['scrape_batch_id'] ?? null,
            'professions_edited_manually' => (bool) ($context['professions_edited_manually'] ?? false),
            'source' => $context['source'] ?? null,
        ]);
    }

    private function resolutionError(array $requestedIds, array $missingIds, array $invalidValues, array $professionIds): ?string
    {
        if ($requestedIds === []) {
            return 'La IA no devolvio ningun area_id.';
        }

        if ($invalidValues !== []) {
            return 'La IA devolvio valores de area que no son IDs validos.';
        }

        if ($missingIds !== []) {
            return 'La IA devolvio area_ids inexistentes en el catalogo.';
        }

        if ($professionIds === []) {
            return 'Las areas seleccionadas no tienen profesiones relacionadas en el catalogo.';
        }

        return null;
    }

    private function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }
}
