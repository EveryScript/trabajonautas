<?php

namespace App\Services;

use App\Models\NotificationLog;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseNotificationService
{
    // Send notifications to devices custom
    public function sendBatchTokens(array $device_tokens, int $announce_id, string $company_name, array $users_info)
    {
        $data = [
            'title'        => 'Nueva convocatoria',
            'body'         => $company_name . ' ha publicado una nueva convocatoria para ti en Trabajonautas.com.',
            'click_action' => 'https://trabajonautas.com/convocatoria/' . $announce_id,
            'icon'         => 'storage/img/tbn-icon.ico'
        ];

        return $this->sendNotificationsAndLog(
            $device_tokens,
            $users_info,
            $data,
            NotificationLog::TYPE_NEW_ANNOUNCEMENT,
            $announce_id
        );
    }

    // Send notification message to unnotified devices
    public function sendUnnotifiedTokens(array $device_tokens, array $users_info)
    {
        $data = [
            'title'        => 'Trabajonautas te informa',
            'body'         => 'Revisamos todas las convocatorias publicadas hoy en todo el país, pero no encontramos ninguna para tu profesión. ¡Ánimo, mañana volveremos a intentarlo!',
            'click_action' => 'https://trabajonautas.com/panel',
            'icon'         => 'storage/img/tbn-icon.ico'
        ];

        return $this->sendNotificationsAndLog(
            $device_tokens,
            $users_info,
            $data,
            NotificationLog::TYPE_UNNOTIFIED_DAILY
        );
    }

    // Send notification message to clients before 2 days expiration PRO or PRO-MAX account
    public function sendExpiringAccountTokens(array $device_tokens, array $users_info)
    {
        $data = [
            'title'        => 'Tu cuenta está por caducar',
            'body'         => 'Tu cuenta está a 2 días de vencer. Renueva tu cuenta para seguir disfrutando de Trabajonautas.',
            'click_action' => 'https://trabajonautas.com/panel',
            'icon'         => 'storage/img/tbn-icon.ico'
        ];

        return $this->sendNotificationsAndLog(
            $device_tokens,
            $users_info,
            $data,
            NotificationLog::TYPE_EXPIRING_ACCOUNT
        );
    }

    private function sendNotificationsAndLog(array $deviceTokens, array $usersInfo, array $messageData, string $notificationType, $announcementId = null): array
    {
        $messaging = Firebase::messaging();
        $message = CloudMessage::new()->withData($messageData);

        $report = $messaging->sendMulticast($message, $deviceTokens);

        $now = now();
        $logData = collect($usersInfo)->map(fn($user) => [
            'user_id'           => $user['user_id'],
            'device_token'      => $user['device_token'],
            'announcement_id'   => $announcementId,
            'notification_type' => $notificationType,
            'sent_at'           => $now,
            'created_at'        => $now,
            'updated_at'        => $now,
        ])->toArray();

        NotificationLog::insert($logData);

        return [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count()
        ];
    }
}
