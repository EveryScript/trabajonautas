<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('companies_list'));
        static::updated(fn() => Cache::forget('companies_list'));
        static::deleted(fn() => Cache::forget('companies_list'));
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
    public function companyType(): BelongsTo
    {
        return $this->belongsTo(CompanyType::class);
    }
}
