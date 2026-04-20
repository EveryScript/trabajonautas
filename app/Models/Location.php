<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Location extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('locations_list'));
        static::updated(fn() => Cache::forget('locations_list'));
        static::deleted(fn() => Cache::forget('locations_list'));
    }

    // Permissions
    public $guarded = [];

    // Relationships
    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
