<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SicoesScrapeBatch extends Model
{
    public const SOURCE_CONSULTING = 'consulting_services';

    public const SOURCE_PERSONNEL = 'personnel_requirements';

    public const SOURCE_TYPES = [
        self::SOURCE_CONSULTING,
        self::SOURCE_PERSONNEL,
    ];

    public const STATUS_QUEUED = 'queued';

    public const STATUS_RUNNING = 'running';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_FAILED = 'failed';

    public const STATUS_FINISHED_LEGACY = 'finished';

    public const ACTIVE_STATUSES = [
        self::STATUS_QUEUED,
        self::STATUS_RUNNING,
    ];

    public const TERMINAL_STATUSES = [
        self::STATUS_COMPLETED,
        self::STATUS_PARTIAL,
        self::STATUS_FAILED,
        self::STATUS_FINISHED_LEGACY,
    ];

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

    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES, true);
    }
}
