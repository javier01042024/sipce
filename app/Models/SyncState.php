<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncState extends Model
{
    protected $fillable = [
        'device_id',
        'user_id',
        'last_sync_at',
        'records_sent',
        'records_received',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
        'records_sent' => 'integer',
        'records_received' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
