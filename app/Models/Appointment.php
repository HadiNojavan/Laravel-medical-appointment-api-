<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'doctor_id', 'start_at', 'end_at', 'status', 'cancelled_at',
        'cancel_reason', 'reminded_at',
    ];

    protected $attributes = ['status' => 'pending',];
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'reminded_at' => 'datetime',
            'status' => AppointmentStatus::class,
        ];
    }
}
