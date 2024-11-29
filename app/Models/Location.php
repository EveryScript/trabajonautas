<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

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
