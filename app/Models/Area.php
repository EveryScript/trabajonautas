<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    // Permissions
    public $guarded = [];

    // Relationships
    public function profesions(): BelongsToMany
    {
        return $this->BelongsToMany(Profesion::class);
    }
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function usersOf(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
