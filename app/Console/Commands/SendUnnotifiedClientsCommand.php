<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendUnnotifiedClientsCommand extends Command
{
    // Command Name
    protected $signature = 'trabajonautas:send-unnotified-clients';
    protected $description = 'Envía notificaiones a los clientes PRO-MAX no notificados durante el día.';

    public function handle()
    {
        $today = Carbon::today();

        // Get users unnotified (query)
        $unnotifiedUsersQuery = User::role(config('app.client_role'))
            ->whereHas('account', function ($query) {
                $query->where('account_type_id', 3)->whereNotNull('device_token');
            })
            ->whereDoesntHave('notificationLogs', function ($query) use ($today) {
                $query->where('sent_at', '>=', $today);
            })
            ->with('account:user_id,device_token');

        // Send notifications to unnotified users
        $unnotifiedUsersQuery->chunkById(500, function ($users) {
            $mappedUsers = $users->map(fn($u) => [
                'user_id' => $u->id,
                'device_token' => $u->account->device_token
            ])->toArray();

            if (!empty($mappedUsers)) {
                $tokensOnly = collect($mappedUsers)->pluck('device_token')->toArray();
                $notifier = new FirebaseNotificationService();
                $notifier->sendUnnotifiedTokens($tokensOnly, $mappedUsers);
            }
        });

        $this->info('Proceso de notificaciones diarias completado.');
    }
}
