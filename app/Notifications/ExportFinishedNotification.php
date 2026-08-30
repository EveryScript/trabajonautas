<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExportFinishedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected ?string $zipFileName,
        protected int $totalAdded,
        protected int $totalMissed,
        protected string $exportId,
        protected string $type
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Tu exportación de archivos está lista ({$this->totalAdded} archivos, {$this->totalMissed} faltantes).",
            'zip_file' => $this->zipFileName,
            'download_url' => $this->zipFileName ? route('exports.download', ['file' => $this->zipFileName]) : null,
            'export_id' => $this->exportId,
            'type' => $this->type
        ];
    }

    protected function messageByType(): string
    {
        if ($this->type === 'zip' && $this->totalAdded === 0)
            return 'No se encontraron archivos adjuntos en el rango seleccionado.';

        if ($this->type === 'destroyed' && $this->totalMissed > 0)
            return "Se eliminaron {$this->totalAdded} convocatorias. {$this->totalMissed} se omitieron por seguir vigentes.";

        return match ($this->type) {
            'excel' => 'Tu archivo de Excel está listo',
            'zip' => "Tus archivos están listos ({$this->totalAdded} agregados, {$this->totalMissed} faltantes).",
            'destroyed' => "Se eliminaron {$this->totalAdded} convocatorias correctamente.",
            default => 'Tu exportación está lista.'
        };
    }
}
