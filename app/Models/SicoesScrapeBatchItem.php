<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SicoesScrapeBatchItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'eligible' => 'boolean',
        'analysis_result' => 'array',
        'ai_metadata' => 'array',
        'removed_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SicoesScrapeBatch::class, 'batch_id');
    }

    public function preview(): BelongsTo
    {
        return $this->belongsTo(BotVacancyPreview::class, 'preview_id');
    }
}
