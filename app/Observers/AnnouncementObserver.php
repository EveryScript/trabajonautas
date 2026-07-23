<?php

namespace App\Observers;

use App\Models\Announcement;
use App\Models\BotVacancyPreview;
use App\Models\SicoesScrapeBatchItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementObserver
{
    private const REOPEN_SNAPSHOTS_RELATION = 'botPreviewReopenSnapshots';

    public function created(Announcement $announcement): void {}

    public function updated(Announcement $announcement): void {}

    public function deleting(Announcement $announcement): void
    {
        $snapshots = BotVacancyPreview::query()
            ->where('convocatoria_id', $announcement->getKey())
            ->where('status', 'published')
            ->get(['id', 'status', 'scrape_batch_id'])
            ->map(fn (BotVacancyPreview $preview): array => [
                'id' => (int) $preview->id,
                'status' => (string) $preview->status,
                'scrape_batch_id' => $preview->scrape_batch_id,
            ]);

        $announcement->setRelation(self::REOPEN_SNAPSHOTS_RELATION, $snapshots);

        DB::table('jobs')
            ->where('payload', 'like', '%"id":' . $announcement->id . '%')
            ->where('payload', 'like', '%SendAnnouncementNotifications%')
            ->delete();

        Log::info("Jobs eliminados para la convocatoria {$announcement->id}");
    }

    public function deleted(Announcement $announcement): void
    {
        if (! $announcement->relationLoaded(self::REOPEN_SNAPSHOTS_RELATION)) {
            return;
        }

        $snapshots = collect($announcement->getRelation(self::REOPEN_SNAPSHOTS_RELATION));

        if ($snapshots->isEmpty()) {
            return;
        }

        $reopened = DB::transaction(function () use ($announcement, $snapshots): int {
            $count = 0;

            foreach ($snapshots as $snapshot) {
                $query = BotVacancyPreview::query()
                    ->whereKey((int) $snapshot['id'])
                    ->where('status', 'published')
                    ->where(function ($query) use ($announcement): void {
                        $query
                            ->whereNull('convocatoria_id')
                            ->orWhere('convocatoria_id', $announcement->getKey());
                    });

                if ($snapshot['scrape_batch_id']) {
                    $query->where('scrape_batch_id', $snapshot['scrape_batch_id']);
                } else {
                    $query->whereNull('scrape_batch_id');
                }

                $preview = $query->lockForUpdate()->first();

                if (! $preview) {
                    continue;
                }

                $rawData = is_array($preview->raw_data) ? $preview->raw_data : [];
                $history = collect(data_get($rawData, 'reopen_history', []))
                    ->filter(fn (mixed $entry): bool => is_array($entry))
                    ->take(-19)
                    ->values()
                    ->all();
                $history[] = [
                    'announcement_id' => (int) $announcement->getKey(),
                    'previous_status' => (string) $snapshot['status'],
                    'previous_batch_id' => $snapshot['scrape_batch_id'],
                    'reopened_at' => now()->toIso8601String(),
                ];
                $rawData['reopen_history'] = $history;

                $preview->update([
                    'status' => 'preview',
                    'convocatoria_id' => null,
                    'removed_from_batch_at' => null,
                    'raw_data' => $rawData,
                ]);

                if ($snapshot['scrape_batch_id']) {
                    SicoesScrapeBatchItem::query()
                        ->where('batch_id', $snapshot['scrape_batch_id'])
                        ->where('preview_id', $preview->id)
                        ->where('status', 'published')
                        ->whereNull('removed_at')
                        ->update([
                            'status' => 'preview',
                            'updated_at' => now(),
                        ]);
                }

                $count++;
            }

            return $count;
        });

        if ($reopened > 0) {
            Log::info('Previews BOT reabiertos tras eliminar convocatoria.', [
                'announcement_id' => $announcement->getKey(),
                'previews_reopened' => $reopened,
            ]);
        }
    }

    public function restored(Announcement $announcement): void {}

    public function forceDeleted(Announcement $announcement): void {}
}
