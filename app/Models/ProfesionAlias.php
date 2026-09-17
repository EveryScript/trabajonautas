<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfesionAlias extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function profesion(): BelongsTo
    {
        return $this->belongsTo(Profesion::class);
    }
}
