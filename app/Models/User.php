<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
        'location_id',
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
    public function areas(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function myAreas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class);
    }
    public function myAnnounces(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class);
    }
    public function myProfesions(): BelongsToMany
    {
        return $this->belongsToMany(Profesion::class);
    }
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
