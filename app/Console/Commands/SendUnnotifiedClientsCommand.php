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

        $notifiedTokens = NotificationLog::whereDate('sent_at', $today)
            ->pluck('device_token')
            ->toArray();

        $unnotifiedTokens = User::role(config('app.client_role'))
            ->whereHas('account', function ($query) use ($notifiedTokens) {
                $query->where('account_type_id', 3)->whereNotNull('device_token')->whereNotIn('device_token', $notifiedTokens);
            })
            ->with('account')
            ->get()
            ->pluck('account.device_token')
            ->unique()
            ->toArray();

        if (!empty($unnotifiedTokens)) {
            $notifier = new FirebaseNotificationService();
            $notifier->sendUnnotifiedTokens($unnotifiedTokens);
            $this->info('Notificaciones enviadas exitosamente.');
        } else {
            $this->info('No hay clientes pendientes de notificación.');
        }
    }
}
