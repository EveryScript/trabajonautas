<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    use HasFactory;
    // Permissions
    public $guarded = [];
    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    // Set default image with condition
    public function image(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value)
                    return 'noticias/default.webp';
                return $value;
            }
        );
    }
}
