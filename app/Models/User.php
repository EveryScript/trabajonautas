<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\CustomVerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{

    use HasApiTokens;
    use HasProfilePhoto;
    use TwoFactorAuthenticatable;
    use Notifiable;
    use HasFactory;
    use HasRoles;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'location_id',
        'gender',
        'age',
        'register_completed',
        'actived',
        'phone',
        'location_id',
        'profesion_id',
        'grade_profile_id',
        'last_announce_check',
        'provider', // Laravel Socialite
        'provider_id', // Laravel Socialite
        'email_verified_at', // Laravel Socialite
        'terms_accepted_at', // Terms and conditions
        'deleted_at' // Soft deletes
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    // Relationships
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }
    public function gradeProfile(): BelongsTo
    {
        return $this->belongsTo(GradeProfile::class);
    }
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'user_id');
    }
    public function profesion(): BelongsTo
    {
        return $this->belongsTo(Profesion::class);
    }
    public function myAnnounces(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class);
    }
    public function notices(): BelongsToMany
    {
        return $this->belongsToMany(Notice::class);
    }
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
    // Latest pending subscription (with Account Type)
    public function latestPendingSubscription()
    {
        return $this->hasOne(Subscription::class)->where('verified_payment', false)->latestOfMany();
    }
    // Latest verified subscription (with Account Type)
    public function latestVerifiedSubscription()
    {
        return $this->hasOne(Subscription::class)->where('verified_payment', true)->latestOfMany();
    }
    // Reset password notification (mail content)
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
    // Email verification notification (mail content)
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmailNotification());
    }
    // Format on set and get phone (country code)
    protected function phone(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                return str_replace('+591', '', $value);
            },

            // MUTADOR: Cuando guardas el dato desde el formulario hacia la BD
            set: function ($value) {
                if (!$value) return null;
                $onlyNumbers = preg_replace('/\D/', '', $value);
                $cleanNumber = ltrim($onlyNumbers, '591');
                return '+591' . $cleanNumber;
            },
        );
    }
}
