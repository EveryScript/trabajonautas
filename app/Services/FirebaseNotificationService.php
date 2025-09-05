<?php

namespace App\Services;

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
                'body' => 'Trabajonautas ha publicado una convocatoria ideal para ti.',
                'click_action' => 'https://trabajonautas.com/convocatoria/' . $announce_id,
                'icon' => 'storage/img/tbn-icon.ico'
            ]);
        $report = $messaging->sendMulticast($message, $device_tokens);
        return [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count()
        ];
    }
}
