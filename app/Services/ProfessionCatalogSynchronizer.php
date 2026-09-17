<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Profesion;
use App\Models\ProfesionAlias;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ProfessionCatalogSynchronizer
{
    private const CACHE_KEYS = [
        'areas',
        'profesions',
        'announcement_profesions_with_areas',
        'profesions_with_areas',
        'profesions_list',
    ];

    public function __construct(
        private ProfessionNameNormalizer $normalizer,
    ) {}

    public function synchronize(
        array $areas,
        array $professions,
        array $aliases = [],
        ?string $userId = null,
        bool $dryRun = false,
    ): array {
        $owner = $this->resolveAdministrator($userId);

        return DB::transaction(function () use ($areas, $professions, $aliases, $owner, $dryRun): array {
            $plan = $this->buildPlan($areas, $professions, $aliases);

            if ($plan['errors'] !== []) {
                if ($dryRun) {
                    return $this->publicResult($plan, true);
                }

                throw new RuntimeException(
                    "El catálogo no se modificó porque se detectaron conflictos:\n"
                    .implode("\n", $plan['errors']),
                );
            }

            if (! $dryRun) {
                if ($plan['aliases'] !== [] && ! Schema::hasTable('profesion_aliases')) {
                    throw new RuntimeException(
                        'La tabla profesion_aliases no existe. Ejecute primero las migraciones pendientes.',
                    );
                }

                $this->applyPlan($plan, $owner);
                $this->clearCaches();
            }

            return $this->publicResult($plan, $dryRun);
        }, 3);
    }

    public function resolveAdministrator(?string $userId = null): User
    {
        $role = trim((string) config('app.admin_role', 'ADMIN')) ?: 'ADMIN';
        $query = User::query()
            ->where('actived', true)
            ->whereHas('roles', fn ($roles) => $roles
                ->where('name', $role)
                ->where('guard_name', 'web'));

        if ($userId) {
            $user = $query->whereKey($userId)->first();

            if (! $user) {
                throw new RuntimeException(
                    'El usuario indicado no existe, está inactivo o no tiene el rol ADMIN para guard web.',
                );
            }

            return $user;
        }

        $admins = $query->orderBy('id')->limit(2)->get();

        if ($admins->isEmpty()) {
            throw new RuntimeException(
                'No existe un usuario ADMIN activo. Use --user cuando haya un administrador válido.',
            );
        }

        if ($admins->count() > 1) {
            throw new RuntimeException(
                'Existe más de un ADMIN activo. Indique explícitamente el propietario con --user.',
            );
        }

        return $admins->first();
    }

    private function buildPlan(array $areas, array $professions, array $aliases): array
    {
        $plan = [
            'areas' => [],
            'professions' => [],
            'relations' => [],
            'aliases' => [],
            'counts' => [
                'areas_created' => 0,
                'areas_updated' => 0,
                'professions_created' => 0,
                'professions_updated' => 0,
                'relations_created' => 0,
                'aliases_created' => 0,
                'aliases_updated' => 0,
            ],
            'errors' => $this->validateSourceCatalog($areas, $professions, $aliases),
            'warnings' => [],
        ];

        $existingAreas = Area::query()->get(['id', 'area_name', 'description', 'user_id']);
        $existingAreaById = $existingAreas->keyBy(fn (Area $area): int => (int) $area->id);
        $existingAreaByName = $existingAreas->keyBy(
            fn (Area $area): string => $this->normalizer->normalize($area->area_name),
        );
        $areaMap = [];

        foreach ($areas as $source) {
            $sourceId = (int) $source['id'];
            $name = trim((string) $source['area_name']);
            $normalizedName = $this->normalizer->normalize($name);
            $byId = $existingAreaById->get($sourceId);
            $byName = $existingAreaByName->get($normalizedName);

            if ($byId && $this->normalizer->normalize($byId->area_name) !== $normalizedName) {
                $plan['errors'][] = "El ID de área {$sourceId} ya pertenece a '{$byId->area_name}', no a '{$name}'.";

                continue;
            }

            if ($byId && $byName && (int) $byId->id !== (int) $byName->id) {
                $plan['errors'][] = "El área '{$name}' coincide por nombre e ID con registros diferentes.";

                continue;
            }

            $existing = $byId ?: $byName;
            $actualId = (int) ($existing?->id ?? $sourceId);
            $create = ! $existing;
            $update = $existing && (
                trim((string) $existing->area_name) !== $name
                || trim((string) $existing->description) !== trim((string) $source['description'])
            );

            $areaMap[$sourceId] = $actualId;
            $plan['areas'][] = [
                'id' => $actualId,
                'area_name' => $name,
                'description' => trim((string) $source['description']),
                'create' => $create,
                'update' => $update,
            ];
            if ($create) {
                $plan['counts']['areas_created']++;
            } elseif ($update) {
                $plan['counts']['areas_updated']++;
            }
        }

        $existingProfessions = Profesion::query()->get(['id', 'profesion_name', 'user_id']);
        $existingProfessionById = $existingProfessions->keyBy(fn (Profesion $profession): int => (int) $profession->id);
        $existingProfessionByName = $existingProfessions->keyBy(
            fn (Profesion $profession): string => $this->normalizer->normalize($profession->profesion_name),
        );
        $professionMap = [];

        foreach ($professions as $source) {
            $sourceId = (int) $source['id'];
            $name = trim((string) $source['profesion_name']);
            $normalizedName = $this->normalizer->normalize($name);
            $byId = $existingProfessionById->get($sourceId);
            $byName = $existingProfessionByName->get($normalizedName);

            if ($byId && $this->normalizer->normalize($byId->profesion_name) !== $normalizedName) {
                $plan['errors'][] = "El ID de profesión {$sourceId} ya pertenece a '{$byId->profesion_name}', no a '{$name}'.";

                continue;
            }

            if ($byId && $byName && (int) $byId->id !== (int) $byName->id) {
                $plan['errors'][] = "La profesión '{$name}' coincide por nombre e ID con registros diferentes.";

                continue;
            }

            $existing = $byId ?: $byName;
            $actualId = (int) ($existing?->id ?? $sourceId);
            $create = ! $existing;
            $update = $existing && trim((string) $existing->profesion_name) !== $name;

            $professionMap[$sourceId] = $actualId;
            $plan['professions'][] = [
                'id' => $actualId,
                'profesion_name' => $name,
                'create' => $create,
                'update' => $update,
            ];
            if ($create) {
                $plan['counts']['professions_created']++;
            } elseif ($update) {
                $plan['counts']['professions_updated']++;
            }
        }

        foreach ($professions as $source) {
            $sourceAreaId = (int) $source['area_id'];
            $sourceProfessionId = (int) $source['id'];
            $areaId = $areaMap[$sourceAreaId] ?? null;
            $professionId = $professionMap[$sourceProfessionId] ?? null;

            if (! $areaId || ! $professionId) {
                continue;
            }

            $exists = DB::table('area_profesion')
                ->where('area_id', $areaId)
                ->where('profesion_id', $professionId)
                ->exists();
            $plan['relations'][] = [
                'area_id' => $areaId,
                'profesion_id' => $professionId,
                'create' => ! $exists,
            ];
            $plan['counts']['relations_created'] += $exists ? 0 : 1;
        }

        if (! Schema::hasTable('profesion_aliases')) {
            if ($aliases !== []) {
                $plan['warnings'][] = 'La tabla profesion_aliases aún no existe; los aliases se muestran sólo como plan.';
            }

            foreach ($aliases as $source) {
                $professionName = trim((string) $source['profesion_name']);
                $professionPlan = collect($plan['professions'])->first(
                    fn (array $profession): bool => $this->normalizer->normalize($profession['profesion_name'])
                        === $this->normalizer->normalize($professionName),
                );
                if (! $professionPlan) {
                    continue;
                }

                $alias = trim((string) $source['alias']);
                $plan['aliases'][] = [
                    'profesion_id' => (int) $professionPlan['id'],
                    'alias' => $alias,
                    'alias_normalizado' => $this->normalizer->normalize($alias),
                    'create' => true,
                    'update' => false,
                ];
                $plan['counts']['aliases_created']++;
            }

            return $plan;
        }

        $professionIdByName = collect($plan['professions'])->keyBy(
            fn (array $profession): string => $this->normalizer->normalize($profession['profesion_name']),
        );
        $existingAliases = ProfesionAlias::query()->get();

        foreach ($aliases as $source) {
            $professionName = trim((string) $source['profesion_name']);
            $professionPlan = $professionIdByName->get($this->normalizer->normalize($professionName));

            if (! $professionPlan) {
                $plan['errors'][] = "El alias '{$source['alias']}' apunta a una profesión oficial inexistente: '{$professionName}'.";

                continue;
            }

            $alias = trim((string) $source['alias']);
            $normalizedAlias = $this->normalizer->normalize($alias);
            $existing = $existingAliases->firstWhere('alias_normalizado', $normalizedAlias);

            if ($existing && (int) $existing->profesion_id !== (int) $professionPlan['id']) {
                $plan['errors'][] = "El alias '{$alias}' ya apunta a otra profesión.";

                continue;
            }

            $create = ! $existing;
            $update = $existing && trim((string) $existing->alias) !== $alias;
            $plan['aliases'][] = [
                'profesion_id' => (int) $professionPlan['id'],
                'alias' => $alias,
                'alias_normalizado' => $normalizedAlias,
                'create' => $create,
                'update' => $update,
            ];
            if ($create) {
                $plan['counts']['aliases_created']++;
            } elseif ($update) {
                $plan['counts']['aliases_updated']++;
            }
        }

        return $plan;
    }

    private function applyPlan(array $plan, User $owner): void
    {
        foreach ($plan['areas'] as $area) {
            if (! $area['create'] && ! $area['update']) {
                continue;
            }

            $existing = Area::query()->find($area['id']);
            $values = [
                'area_name' => $area['area_name'],
                'description' => $area['description'],
            ];
            if (! $existing) {
                $values['user_id'] = $owner->getKey();
            }

            Area::query()->updateOrCreate(['id' => $area['id']], $values);
        }

        foreach ($plan['professions'] as $profession) {
            if (! $profession['create'] && ! $profession['update']) {
                continue;
            }

            $existing = Profesion::query()->find($profession['id']);
            $values = ['profesion_name' => $profession['profesion_name']];
            if (! $existing) {
                $values['user_id'] = $owner->getKey();
            }

            Profesion::query()->updateOrCreate(['id' => $profession['id']], $values);
        }

        foreach ($plan['relations'] as $relation) {
            if (! $relation['create']) {
                continue;
            }

            DB::table('area_profesion')->updateOrInsert(
                [
                    'area_id' => $relation['area_id'],
                    'profesion_id' => $relation['profesion_id'],
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        foreach ($plan['aliases'] as $alias) {
            if (! $alias['create'] && ! $alias['update']) {
                continue;
            }

            ProfesionAlias::query()->updateOrCreate(
                ['alias_normalizado' => $alias['alias_normalizado']],
                [
                    'profesion_id' => $alias['profesion_id'],
                    'alias' => $alias['alias'],
                ],
            );
        }
    }

    private function validateSourceCatalog(array $areas, array $professions, array $aliases): array
    {
        $errors = [];
        $areaIds = collect($areas)->pluck('id')->map(fn ($id): int => (int) $id);
        $professionIds = collect($professions)->pluck('id')->map(fn ($id): int => (int) $id);
        $professionNames = collect($professions)
            ->pluck('profesion_name')
            ->map(fn ($name): string => $this->normalizer->normalize((string) $name));

        foreach ([
            'IDs de área' => $areaIds,
            'nombres de área' => collect($areas)->pluck('area_name')->map(fn ($name) => $this->normalizer->normalize((string) $name)),
            'IDs de profesión' => $professionIds,
            'nombres de profesión' => $professionNames,
            'aliases' => collect($aliases)->pluck('alias')->map(fn ($alias) => $this->normalizer->normalize((string) $alias)),
        ] as $label => $values) {
            $duplicates = $values->duplicates()->unique()->values();
            if ($duplicates->isNotEmpty()) {
                $errors[] = "{$label} duplicados: ".$duplicates->implode(', ');
            }
        }

        $missingAreas = collect($professions)
            ->pluck('area_id')
            ->map(fn ($id): int => (int) $id)
            ->diff($areaIds)
            ->unique()
            ->values();
        if ($missingAreas->isNotEmpty()) {
            $errors[] = 'Relaciones con áreas ausentes: '.$missingAreas->implode(', ');
        }

        $missingAliasTargets = collect($aliases)
            ->pluck('profesion_name')
            ->map(fn ($name): string => $this->normalizer->normalize((string) $name))
            ->diff($professionNames)
            ->unique()
            ->values();
        if ($missingAliasTargets->isNotEmpty()) {
            $errors[] = 'Aliases con profesiones oficiales ausentes: '.$missingAliasTargets->implode(', ');
        }

        $aliasesCollidingWithOfficialNames = collect($aliases)
            ->pluck('alias')
            ->map(fn ($alias): string => $this->normalizer->normalize((string) $alias))
            ->intersect($professionNames)
            ->unique()
            ->values();
        if ($aliasesCollidingWithOfficialNames->isNotEmpty()) {
            $errors[] = 'Aliases que colisionan con nombres oficiales: '
                .$aliasesCollidingWithOfficialNames->implode(', ');
        }

        return $errors;
    }

    private function clearCaches(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }

    private function publicResult(array $plan, bool $dryRun): array
    {
        return [
            ...$plan['counts'],
            'errors' => array_values(array_unique($plan['errors'])),
            'warnings' => array_values(array_unique($plan['warnings'])),
            'dry_run' => $dryRun,
        ];
    }
}
