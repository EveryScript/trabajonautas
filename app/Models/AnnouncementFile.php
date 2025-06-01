<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function announcement():BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }
}
