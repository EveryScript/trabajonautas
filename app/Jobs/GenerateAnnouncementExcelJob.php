<?php

namespace App\Jobs;

use App\Exports\AnnouncesExport;
use App\Models\User;
use App\Notifications\ExportFinishedNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class GenerateAnnouncementExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;
    public int $tries = 1;

    public function __construct(
        protected Carbon $from,
        protected Carbon $to,
        protected string $userId,
        protected string $exportId,
    ) {}

    public function handle(): void
    {
        $fileName = 'TBNConvocatorias_' . now()->timestamp . '.xlsx';
        Excel::store(new AnnouncesExport($this->from, $this->to), 'exports/' . $fileName);

        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new ExportFinishedNotification(
                zipFileName: $fileName,
                totalAdded: 0,
                totalMissed: 0,
                exportId: $this->exportId,
                type: 'excel'
            ));
        }
    }
}
