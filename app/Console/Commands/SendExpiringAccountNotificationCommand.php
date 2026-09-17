<?php

namespace App\Console\Commands;

use App\Mail\AccountWillExpireSoon;
use App\Models\NotificationLog;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendExpiringAccountNotificationCommand extends Command
{
    protected $signature = 'trabajonautas:send-expiring-account-notification-clients';
    protected $description = 'Send a main and notification when their account PRO or PRO-MAX is 2 days before expiration';

    public function handle()
    {
        $twoDaysFromNow = Carbon::today()->addDays(2);

        // Get clients where account will be expired in 2 days
        $baseQuery = User::role(config('app.client_role'))
            ->whereHas('account', function ($query) use ($twoDaysFromNow) {
                $query->whereIn('account_type_id', [2, 3])
                    ->whereDate('limit_time', $twoDaysFromNow);
            })
            // Check if log is already exist
            ->whereDoesntHave('notificationLogsExist', function ($query) {
                $query->where('notification_type', NotificationLog::TYPE_EXPIRING_ACCOUNT)
                    ->whereDate('created_at', Carbon::today());
            })
            ->with('account:user_id,account_type_id,device_token,limit_time');

        // Send email and notification to every client 
        $baseQuery->chunkById(200, function ($users) {
            $tokensForFirebase = [];
            $usersToLog = [];

            foreach ($users as $client) {
                Mail::to($client->email)->queue(new AccountWillExpireSoon($client));

                $deviceToken = $client->account?->device_token;
                if ($deviceToken) {
                    $tokensForFirebase[] = $deviceToken;
                    $usersToLog[] = [
                        'user_id'      => $client->id,
                        'device_token' => $deviceToken,
                    ];
                }
            }

            // 5. Send notifications to Firebase
            if (!empty($tokensForFirebase)) {
                $notifier = new FirebaseNotificationService();
                $notifier->sendExpiringAccountTokens($tokensForFirebase, $usersToLog);
            }
        });

        $this->info("Total users notified and mailed: {$baseQuery ->count()}");
    }
}
