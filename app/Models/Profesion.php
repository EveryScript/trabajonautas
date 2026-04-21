<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Profesion extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('profesions'));
        static::deleted(fn() => Cache::forget('profesions'));
    }

    // Permissions
    public $guarded = [];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class);
    }
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
