<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SicoesScrapeBatch extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'requested_date' => 'date',
        'summary' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function botCompany(): BelongsTo
    {
        return $this->belongsTo(BotCompany::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SicoesScrapeBatchItem::class, 'batch_id');
    }
}
