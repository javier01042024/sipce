<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientActivityLog extends Model
{
    protected $table = 'patient_activity_log';

    protected $fillable = [
        'user_id',
        'accion',
        'descripcion',
        'ip',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
