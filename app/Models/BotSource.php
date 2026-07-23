<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BotSource extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function companies(): HasMany
    {
        return $this->hasMany(BotCompany::class);
    }
}
