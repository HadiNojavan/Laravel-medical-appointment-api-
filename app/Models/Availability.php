<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'rrule', 'start_time', 'end_time', 'slot_duration_minutes', 'valid_from',
        'valid_until', 'timezone', 'is_active',];

    protected $attributes = ['timezone' => 'Asia/Tehran', 'is_active' => true,];
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

}
