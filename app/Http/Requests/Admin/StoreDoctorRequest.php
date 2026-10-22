<?php

namespace App\Http\Requests\Admin;

use App\Models\Doctor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Doctor::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string','min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'lowercase', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3', 'max:255'],
            'specialty_id' => ['required', 'integer', 'exists:specialties,id'],
            'medical_license_number' => ['required', 'string', 'max:50', 'unique:doctors,medical_license_number'],
            'bio' => ['nullable', 'string'],

        ];
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json(['error' => 'You are not authorized to perform this action.'],
            403));
    }


}
