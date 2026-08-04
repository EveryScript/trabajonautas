<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_type_id',
        'price',
        'verified_payment',
        'verified_by_user_id',
        'qr_id', // Baneco Dinamic QR
        'qr_image', // Baneco Dinamic QR
        'qr_expires_at' // Baneco Dinamic QR
    ];

    protected $casts = [
        'qr_expires_at' => 'datetime',
        'verified_payment' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
    public function type(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }
}
