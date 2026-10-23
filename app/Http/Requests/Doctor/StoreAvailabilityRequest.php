<?php

namespace App\Http\Requests\Doctor;

use App\Models\Availability;
use App\Rules\ValidRecurrenceRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Availability::class);
    }

    /*
     * here after passing authorize method
     * we convert lowercase character to uppercase
     * and trim it
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->rrule)) {
            $this->merge(['rrule' => strtoupper(trim($this->rrule))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rrule' => ['required', 'string', 'max:255', new ValidRecurrenceRule()],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'valid_from' => ['required', 'date_format:Y-m-d'],
            'valid_until' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:valid_from'],
            'timezone' => ['sometimes', 'required', 'timezone:all'],
        ];
    }
}
