<?php

namespace App\Livewire\Announcement;

use App\Exports\AnnouncesExport;
use App\Jobs\DeleteAnnouncementsJob;
use App\Jobs\ExportAnnouncementFilesJob;
use App\Jobs\GenerateAnnouncementExcelJob;
use App\Models\Announcement;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Str;

class ExportAnnouncement extends Component
{
    public string $from = '';
    public string $to = '';
    public string $exportId = '';

    public ?string $statusExcel = null;
    public ?string $statusZip = null;
    public ?string $statusDeleting = null;

    public ?string $urlExcel = null;
    public ?string $urlZip = null;
    public bool $zipWithoutFiles = false;

    public bool $confirmDeleted = false;
    public string $textConfirmation = '';

    public int $totalDeletable = 0;
    public int $totalNotDeletable = 0;

    public function mount()
    {
        $this->to = now()->format('Y-m-d');
        $this->from = now()->subDays(30)->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ];
    }

    public function setDateRange($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function startExcel()
    {
        $this->validate();
        $this->exportId = $this->exportId ?: (string) Str::uuid();
        $this->statusExcel = 'procesing';

        GenerateAnnouncementExcelJob::dispatch(
            Carbon::parse($this->from)->startOfDay(),
            Carbon::parse($this->to)->endOfDay(),
            auth()->id(),
            $this->exportId
        );
    }

    public function startZip()
    {
        $this->statusZip = 'procesing';

        ExportAnnouncementFilesJob::dispatch(
            Carbon::parse($this->from)->startOfDay(),
            Carbon::parse($this->to)->endOfDay(),
            auth()->id(),
            $this->exportId
        );
    }

    public function startDeleting()
    {
        if ($this->textConfirmation !== 'ELIMINAR') {
            $this->addError('textConfirmation', 'Debes escribir ELIMINAR para confirmar');
            return;
        }

        $this->statusDeleting = 'procesing';

        DeleteAnnouncementsJob::dispatch(
            Carbon::parse($this->from)->startOfDay(),
            Carbon::parse($this->to)->endOfDay(),
            auth()->id(),
            $this->exportId
        );
    }

    public function checkStatus()
    {
        if (!$this->exportId) return;

        /** @var \App\Models\User $user */
        $user = auth()->user();

        /** @var \Illuminate\Notifications\DatabaseNotificationCollection $notifications */
        $notifications = $user
            ->unreadNotifications()
            ->where('data->export_id', $this->exportId)
            ->get();

        foreach ($notifications as $notification) {
            match ($notification->data['type']) {
                'excel' => $this->setReadyExcel($notification),
                'zip' => $this->setReadyZip($notification),
                'destroyed' => $this->setReadyDeleting($notification),
                default => null,
            };
        }
    }

    protected function setReadyExcel($notification)
    {
        $this->urlExcel = $notification->data['download_url'];
        $this->statusExcel = 'ready';
        $notification->markAsRead();
    }

    protected function setReadyZip($notification)
    {
        if ($notification->data['download_url'] === null)
            $this->zipWithoutFiles = true;
        else
            $this->urlZip = $notification->data['download_url'];
        $this->statusZip = 'ready';

        $notification->markAsRead();
    }

    protected function setReadyDeleting($notificacion)
    {
        $this->statusDeleting = 'ready';
        $notificacion->markAsRead();
    }

    public function restartAll()
    {
        $this->reset([
            'exportId',
            'statusExcel',
            'statusZip',
            'statusDeleting',
            'urlExcel',
            'urlZip',
            'confirmDeleted',
            'textConfirmation',
        ]);
    }

    protected function calculateDeletable()
    {
        if (!$this->from || !$this->to) {
            $this->totalDeletable = 0;
            $this->totalNotDeletable = 0;
            return;
        }

        $from = Carbon::parse($this->from)->startOfDay();
        $to = Carbon::parse($this->to)->endOfDay();

        $totalInRange = Announcement::whereBetween('created_at', [$from, $to])->count();

        $this->totalDeletable = Announcement::whereBetween('created_at', [$from, $to])
            ->where('expiration_time', '<', now())
            ->count();

        $this->totalNotDeletable = $totalInRange - $this->totalDeletable;
    }

    public function updatedFrom()
    {
        $this->calculateDeletable();
    }

    public function updatedTo()
    {
        $this->calculateDeletable();
    }

    public function render()
    {
        return view('livewire.announcement.export-announcement');
    }
}
