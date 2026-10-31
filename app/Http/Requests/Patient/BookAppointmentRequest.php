<?php

namespace App\Http\Requests\Patient;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Appointment::class);
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'start_at' => ['required', 'date_format:Y-m-d\TH:i:s\Z'],
        ];
    }
}
