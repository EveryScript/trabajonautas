<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    public const TYPE_NEW_ANNOUNCEMENT = 'new_announcement';
    public const TYPE_UNNOTIFIED_DAILY = 'unnotified_daily';
    public const TYPE_EXPIRING_ACCOUNT = 'expiring_account';

    protected $fillable = [
        'user_id',
        'device_token',
        'announcement_id',
        'notification_type',
        'sent_at'
    ];
}
