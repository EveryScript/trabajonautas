<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnnouncementType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'is_active'];

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function excludedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'excluded_announcement_types')->withTimestamps();
    }
}
