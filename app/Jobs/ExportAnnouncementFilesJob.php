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
use ZipArchive;

class ExportAnnouncementFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;
    public int $tries = 1;

    public function __construct(
        protected Carbon $from,
        protected Carbon $to,
        protected string $userId,
        protected string $exportId
    ) {}

    public function handle(): void
    {
        $zipFileName = 'archivos_convocatorias_' . now()->timestamp . '.zip';
        $relativeZipPath = 'exports/' . $zipFileName;
        $fullZipPath = storage_path('app/' . $relativeZipPath);

        if (!is_dir(dirname($fullZipPath))) {
            mkdir(dirname($fullZipPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            Log::error("No se pudo crear el archivo ZIP en {$fullZipPath}");
            return;
        }

        $totalAdded = 0;
        $totalMissed = 0;

        Announcement::query()
            ->with('announceFiles')
            ->whereBetween('created_at', [$this->from, $this->to])
            ->chunk(200, function ($announcements) use ($zip, &$totalAdded, &$totalMissed) {
                foreach ($announcements as $announce) {
                    foreach ($announce->announceFiles as $file) {
                        if (Storage::disk('public')->exists($file->url)) {
                            $fullPath = storage_path('app/public/' . $file->url);
                            $extension = pathinfo($file->url, PATHINFO_EXTENSION);
                            $nombreEnZip = "convocatoria_{$announce->id}/{$file->original_name}.{$extension}";
                            $zip->addFile($fullPath, $nombreEnZip);
                            $totalAdded++;
                        } else {
                            $totalMissed++;
                            Log::warning("Archivo no encontrado: {$file->url} (convocatoria {$announce->id})");
                        }
                    }
                }
            });

        $zip->close();

        $user = User::find($this->userId);
        if (!$user) return;

        // NO files added
        if ($totalAdded === 0) {
            Log::info("Exportación {$this->exportId}: sin archivos que exportar en el rango.");

            $user->notify(new ExportFinishedNotification(
                zipFileName: null,
                totalAdded: 0,
                totalMissed: (int) $totalMissed,
                exportId: $this->exportId,
                type: 'zip'
            ));

            return;
        }

        Log::info("Exportación {$this->exportId} completada: {$totalAdded} agregados, {$totalMissed} faltantes.");

        $user->notify(new ExportFinishedNotification(
            zipFileName: $zipFileName,
            totalAdded: (int) $totalAdded,
            totalMissed: (int) $totalMissed,
            exportId: $this->exportId,
            type: 'zip'
        ));
    }
}
