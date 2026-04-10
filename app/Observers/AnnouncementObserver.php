<?php

namespace App\Observers;

use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementObserver
{
    public function created(Announcement $announcement): void {}

    public function updated(Announcement $announcement): void {}

    public function deleting(Announcement $announcement): void
    {
        DB::table('jobs')
            ->where('payload', 'like', '%"id":' . $announcement->id . '%')
            ->where('payload', 'like', '%SendAnnouncementNotifications%')
            ->delete();

        Log::info("Jobs eliminados para la convocatoria {$announcement->id}");
    }

    public function restored(Announcement $announcement): void {}

    public function forceDeleted(Announcement $announcement): void {}
}
