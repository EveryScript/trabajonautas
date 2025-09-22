<?php

namespace App\Services;

use App\Models\NotificationLog;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseNotificationService
{
    // Send notifications to devices custom
    public function sendBatchTokens(array $device_tokens, $announce_id)
    {
        $messaging = Firebase::messaging();
        $message = CloudMessage::new()
            ->withData([
                'title' => 'Nueva convocatoria',
                'body' => 'Trabajonautas.com ha publicado una convocatoria ideal para ti.',
                'click_action' => 'https://trabajonautas.com/convocatoria/' . $announce_id,
                'icon' => 'storage/img/tbn-icon.ico'
            ]);
        $report = $messaging->sendMulticast($message, $device_tokens);

        // Register tokens in NotificationLog
        foreach ($device_tokens as $token) {
            NotificationLog::create([
                'device_token' => $token,
                'announcement_id' => $announce_id,
                'sent_at' => now()
            ]);
        }

        return [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count()
        ];
    }

    // Send notification message to unnotified devices
    public function sendUnnotifiedTokens(array $device_tokens)
    {
        $messaging = Firebase::messaging();
        $message = CloudMessage::new()
            ->withData([
                'title' => 'Trabajonautas te informa',
                'body' => 'Revisamos todas las convocatorias pulicadas el día de hoy en todo el país, sin embargo no logramos encontrar alguna para tu profesión. Ánimo, lo volveremos a intentar mañana.',
                'click_action' => 'https://trabajonautas.com/panel',
                'icon' => 'storage/img/tbn-icon.ico'
            ]);

        $report = $messaging->sendMulticast($message, $device_tokens);

        return [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count()
        ];
    }
}
