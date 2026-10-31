<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'start_at' => $this->start_at->toIso8601ZuluString(),
            'end_at' => $this->end_at->toIso8601ZuluString(),
            'cancelled_at' => $this->cancelled_at?->toIso8601ZuluString(),
            'cancel_reason' => $this->cancel_reason,
            'doctor' => new PublicDoctorResource($this->whenLoaded('doctor')),
            'patient' => new UserResource($this->whenLoaded('patient')),
            'created_at' => $this->created_at->toIso8601ZuluString(),
        ];
    }
}
