<?php

namespace App\Services;

use App\Models\NotificationLog;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseNotificationService
{
    // Send notifications to devices custom
    public function sendBatchTokens(array $device_tokens, $announce_id, $company_name, array $users_info)
    {
        $messaging = Firebase::messaging();
        $message = CloudMessage::new()
            ->withData([
                'title' => 'Nueva convocatoria',
                'body' => $company_name . ' ha publicado una nueva convocatoria para ti en Trabajonautas.com.',
                'click_action' => 'https://trabajonautas.com/convocatoria/' . $announce_id,
                'icon' => 'storage/img/tbn-icon.ico'
            ]);
        $report = $messaging->sendMulticast($message, $device_tokens);

        // Register all tokens and users_id in NotificationLog
        $logData = collect($users_info)->map(fn($user) => [
            'user_id'         => $user['user_id'],
            'device_token'    => $user['device_token'],
            'announcement_id' => $announce_id,
            'sent_at'         => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ])->toArray();
        NotificationLog::insert($logData);

        return [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count()
        ];
    }

    // Send notification message to unnotified devices
    public function sendUnnotifiedTokens(array $device_tokens, array $users_info)
    {
        $messaging = Firebase::messaging();
        $message = CloudMessage::new()
            ->withData([
                'title' => 'Trabajonautas te informa',
                'body' => 'Revisamos todas las convocatorias publicadas hoy en todo el país, pero no encontramos ninguna para tu profesión. ¡Ánimo, mañana volveremos a intentarlo!',
                'click_action' => 'https://trabajonautas.com/panel',
                'icon' => 'storage/img/tbn-icon.ico'
            ]);

        $report = $messaging->sendMulticast($message, $device_tokens);

        $logData = collect($users_info)->map(fn($user) => [
            'user_id'         => $user['user_id'],
            'device_token'    => $user['device_token'],
            'announcement_id' => null,
            'sent_at'         => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ])->toArray();

        NotificationLog::insert($logData);

        return [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count()
        ];
    }
}
