<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\Company;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAnnouncementNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Jobs value: Try 3 times after send failed_jobs
    public $backoff = 60; // Jobs value: Wait 60 seconds to try again
    public $announcement;

    /**
     * Create a new job instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Abort if notification is sended
        if ($this->announcement->notification_sent)
            return;

        // Get company data    
        $company = Company::find($this->announcement->company_id);
        if (!$company) return;

        $notifier = new FirebaseNotificationService();

        // Get clients data (PRO-MAX clents with same profesion and location)
        $locationIds = collect($this->announcement->locations)->pluck('id')->toArray();
        $profesionIds = collect($this->announcement->profesions)->pluck('id')->toArray();

        $clients = User::role(config('app.client_role'))
            ->whereHas('account', fn($query) => $query
                ->where('account_type_id', 3)
                ->whereNotNull('device_token'))
            ->whereHas('location', fn($query) => $query
                ->whereIn('location_id', $locationIds))
            ->whereHas('profesion', fn($query)  => $query
                ->whereIn('profesion_id', $profesionIds))
            ->get();

        // Map tokens and user_id
        $mappedUsers = $clients->map(function ($client) {
            return [
                'user_id' => $client->id,
                'device_token' => $client->account->device_token
            ];
        })->unique('device_token')->filter(fn($u) => !empty($u['device_token']));

        // Send tokens and user
        if ($mappedUsers->isNotEmpty()) {
            foreach ($mappedUsers->chunk(500) as $chunk) {
                $tokensOnly = $chunk->pluck('device_token')->toArray();
                $notifier->sendBatchTokens($tokensOnly, $this->announcement->id, $company->company_name, $chunk->toArray());
            }
        }

        // Set notification like "sended"
        $this->announcement->update(['notification_sent' => true]);
    }

    public function failed($exception)
    {
        Log::error("El envío de notificaciones para el anuncio {$this->announcement->id} ha fallado permanentemente.", [
            'error' => $exception->getMessage()
        ]);
    }
}
