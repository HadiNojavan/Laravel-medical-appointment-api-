<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityException extends Model
{
    use HasFactory;
    protected $fillable = ['doctor_id', 'start_at', 'end_at', 'reason',];
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function casts()
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime'
        ];
    }
}
