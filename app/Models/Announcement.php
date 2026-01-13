<?php

namespace App\Models;

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

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
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

    // Special query
    public function announceSuggests()
    {
        return $this->hasMany(Announcement::class, 'area_id', 'area_id');
    }
    // Scope for announcements by area
    public function scopeGetSuggests($query, $currentId, $areaId)
    {
        return $query->where('area_id', $areaId)
            ->where('id', '!=', $currentId)
            ->where('expiration_time', '>=', now())
            ->limit(5);
    }
}
