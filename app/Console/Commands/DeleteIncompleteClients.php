<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteIncompleteClients extends Command
{
    protected $signature = 'trabajonautas:delete-incomplete-clients';
    protected $description = 'Delete users with incomplete registration after 7 days';

    public function handle()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $usersToDelete = User::role(config('app.client_role'))
            ->where('register_completed', false)
            ->where('created_at', '<', $sevenDaysAgo)
            ->get();

        foreach ($usersToDelete as $user) {
            $user->forceDelete();
            $this->info("Deleted user: {$user->email}");
        }

        $this->info("Total users deleted: {$usersToDelete->count()}");
    }
}
