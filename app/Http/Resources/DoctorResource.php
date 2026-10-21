<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'medical_license_number' => $this->medical_license_number,
            'bio' => $this->bio,
            'is_active' => $this->is_active,
            'user' => new UserResource($this->whenLoaded('user')),
            'specialty' => new SpecialtyResource($this->whenLoaded('specialty')),
            'created_at' => $this->created_at,
        ];
    }
}
