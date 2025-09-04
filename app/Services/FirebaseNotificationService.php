<?php

namespace App\Services;

use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseNotificationService
{
    // Send notifications to devices custom
    public function sendBatchTokens(array $device_tokens, $announce_id)
    {
        $messaging = Firebase::messaging();
        $notification = Notification::create(
            'Nueva convocatoria',
            'Trabajonautas ha publicado una convocatoria ideal para ti. Haz click aquí para ver la convocatoria.'
        );
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData([
                'click_action' => 'https://trabajonautas.com/convocatoria/' . $announce_id
            ]);
        $report = $messaging->sendMulticast($message, $device_tokens);
        return [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count()
        ];
    }
}
