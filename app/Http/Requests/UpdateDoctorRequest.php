<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('doctor'));
    }

    public function rules(): array
    {
        /*
         * if admin enter doctor email same as before we wont get unique error
         */
        $doctor = $this->route('doctor');
        return [
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'email' => [
                'sometimes', 'required', 'string', 'email', 'max:255', 'lowercase',
                Rule::unique('users', 'email')->ignore($doctor->user_id),
            ],
            'specialty_id' => ['sometimes', 'required', 'integer', 'exists:specialties,id'],
            'medical_license_number' => [
                'sometimes', 'required', 'string', 'max:50',
                Rule::unique('doctors', 'medical_license_number')->ignore($doctor->id),
            ],
            'bio' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
