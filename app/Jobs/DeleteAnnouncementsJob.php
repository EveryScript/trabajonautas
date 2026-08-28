<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\ExportFinishedNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteAnnouncementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;
    public int $tries = 1;

    public function __construct(
        protected Carbon $from,
        protected Carbon $to,
        protected string $userId,
        protected string $exportId
    ) {}

    public function handle(): void
    {
        $totalDeleted = 0;
        $totalFilesDeleted = 0;
        $totalFilesMissed = 0;
        $totalCurrentOmitted = 0;

        $totalInRange = Announcement::query()
            ->whereBetween('created_at', [$this->from, $this->to])
            ->count();

        Announcement::query()
            ->with('announceFiles')
            ->whereBetween('created_at', [$this->from, $this->to])
            ->where('expiration_time', '<', now())
            ->chunkById(200, function ($announcements) use (
                &$totalDeleted,
                &$totalFilesDeleted,
                &$totalFilesMissed
            ) {
                foreach ($announcements as $announce) {
                    foreach ($announce->announceFiles as $file) {
                        if (Storage::disk('public')->exists($file->url)) {
                            Storage::disk('public')->delete($file->url);
                            $totalFilesDeleted++;
                        } else {
                            $totalFilesMissed++;
                            Log::warning("Archivo ya no existía al intentar borrar: {$file->url} (convocatoria {$announce->id})");
                        }
                    }
                    $announce->announceFiles()->delete();
                    $announce->delete();
                    Log::info("Eliminando convocatoria ID {$announce->id}: {$announce->announce_title}");
                    $totalDeleted++;
                }
            });

        $totalCurrentOmitted = $totalInRange - $totalDeleted;

        Log::info("Eliminación completada: {$totalDeleted} eliminadas, {$totalCurrentOmitted} omitidas por seguir vigentes, {$totalFilesDeleted} archivos borrados (rango {$this->from} - {$this->to}).");


        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new ExportFinishedNotification(
                zipFileName: null,
                totalAdded: (int) $totalDeleted,
                totalMissed: (int) $totalCurrentOmitted,
                exportId: $this->exportId,
                type: 'destroyed'
            ));
        }
    }
}
