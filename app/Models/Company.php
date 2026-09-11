<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Permissions
    public $guarded = [];

    public function getCompanyImageAttribute(?string $value): ?string
    {
        return $value ? ltrim($value, '/') : null;
    }

    public function hasCompanyImageFile(): bool
    {
        if (!$this->company_image)
            return false;

        return Cache::remember('company-image-exists:' . $this->company_image, 3600, fn() => Storage::disk('public')->exists($this->company_image));
    }

    public function companyImageUrl(): ?string
    {
        return $this->company_image ? asset('storage/' . $this->company_image) : null;
    }

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
