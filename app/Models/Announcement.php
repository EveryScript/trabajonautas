<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
