<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeProfile extends Model
{
    use HasFactory;

    // Permissions
    public $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
