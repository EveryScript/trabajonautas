<?php

namespace App\Models;

use App\Observers\AnnouncementObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory;

    // Permissions
    public $guarded = [];

    // Casts
    protected $casts = [
        'expiration_time' => 'datetime',
        'scheduled_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function usersOf(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    public function profesions(): BelongsToMany
    {
        return $this->belongsToMany(Profesion::class);
    }
    public function announceFiles(): HasMany
    {
        return $this->hasMany(AnnouncementFile::class);
    }
    // Observer (delete jobs on delete announcement)
    protected static function booted(): void
    {
        static::observe(AnnouncementObserver::class);
    }
}
