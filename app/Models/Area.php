<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;

class Area extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('areas'));
        static::deleted(fn() => Cache::forget('areas'));
    }

    // Permissions
    public $guarded = [];

    // Relationships
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function profesions(): HasMany
    {
        return $this->hasMany(Profesion::class);
    }
}
