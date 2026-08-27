<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncPending extends Model
{
    protected $table = 'sync_pending';
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'tabla',
        'accion',
        'datos',
        'device_id',
        'user_id',
        'created_local',
        'sincronizado',
        'sincronizado_at',
        'error_sync',
    ];

    protected $casts = [
        'datos' => 'array',
        'created_local' => 'datetime',
        'sincronizado' => 'boolean',
        'sincronizado_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('sincronizado', false);
    }

    public function scopeNoSincronizados($query)
    {
        return $query->where('sincronizado', false)->orderBy('created_local', 'asc');
    }
}
