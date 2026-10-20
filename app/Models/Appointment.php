<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;
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
        ];
    }
}
