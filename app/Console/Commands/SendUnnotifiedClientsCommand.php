<?php

namespace App\Console\Commands;

use App\Mail\HasNewAnnounceMail;
use App\Models\Announcement;
use App\Models\NotificationLog;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendUnnotifiedClientsCommand extends Command
{
    // Command Name
    protected $signature = 'trabajonautas:send-unnotified-clients';
    protected $description = 'Envía notificaiones a los clientes PRO-MAX no notificados durante el día y envía correos a clientes que no han visto su panel.';

    public function handle()
    {
        $today = Carbon::today();

        // 1. Base query (clients PRO and PRO-MAX)
        $baseQuery = User::role(config('app.client_role'))
            ->whereHas('account', function ($query) {
                $query->whereIn('account_type_id', [2, 3]);
            })
            ->with('account:user_id,account_type_id,device_token');

        // 2. Optmize query execution
        $baseQuery->chunkById(200, function ($users) use ($today) {
            $tokensForFirebase = [];
            $usersToLog = [];

            foreach ($users as $client) {
                if (!$client->account) continue;

                $account_type_client = $client->account->account_type_id;

                // 3. Send emails to clents if last_announce_check is not updated (today)
                $hasNewAnnounces = Announcement::where('expiration_time', '>=', now())
                    ->where('updated_at', '>', $client->last_announce_check ?? now()->subDays(7))
                    ->whereHas('locations', fn($q) => $q->where('locations.id', $client->location_id))
                    ->whereHas('profesions', fn($q) => $q->where('profesions.id', $client->profesion_id))
                    ->exists();

                if ($hasNewAnnounces) {
                    Mail::to($client->email)->queue(new HasNewAnnounceMail($client));
                    $client->update(['last_announce_check' => now()]);
                }

                // 4. Select all clients (PRO-MAX) has not notified today
                if ($account_type_client == 3) {
                    $alreadyNotifiedToday = $client->notificationLogs()->whereDate('sent_at', $today)->exists();
                    if (!$alreadyNotifiedToday && $client->account->device_token) {
                        $tokensForFirebase[] = $client->account->device_token;
                        $usersToLog[] = [
                            'user_id' => $client->id,
                            'device_token' => $client->account->device_token
                        ];
                    }
                }
            }

            // 5. Send notifications to Firebase
            if (!empty($tokensForFirebase)) {
                $notifier = new FirebaseNotificationService();
                $notifier->sendUnnotifiedTokens($tokensForFirebase, $usersToLog);
            }
        });

        $this->info('Proceso finalizado. Notificaciones y correos enviados.');
    }
}
