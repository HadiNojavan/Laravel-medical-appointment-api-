<?php

namespace App\Http\Requests\Doctor;

use App\Rules\ValidRecurrenceRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('availability'));
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->rrule)) {
            $this->merge(['rrule' => strtoupper(trim($this->rrule))]);
        }
    }

    public function rules(): array
    {
        return [
            'rrule' => ['sometimes', 'required', 'string', 'max:255', new ValidRecurrenceRule()],
            'start_time' => ['sometimes', 'required', 'date_format:H:i'],
            'end_time' => ['sometimes', 'required', 'date_format:H:i'],
            'slot_duration_minutes' => ['sometimes', 'required', 'integer', 'min:5', 'max:120'],
            'valid_from' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'valid_until' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'timezone' => ['sometimes', 'required', 'timezone:all'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }

    /**
     * Cross-field checks must use the merged values:
     * a partial update may send only one side of the pair.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $availability = $this->route('availability');

                $start = $this->input('start_time', substr($availability->start_time, 0, 5));
                $end = $this->input('end_time', substr($availability->end_time, 0, 5));

                if ($end <= $start) {
                    $validator->errors()->add('end_time', 'The end time must be after the start time.');
                }

                $from = $this->input('valid_from', $availability->valid_from->toDateString());
                $until = $this->has('valid_until')
                    ? $this->input('valid_until')
                    : $availability->valid_until?->toDateString();

                if ($until !== null && Carbon::parse($until)->lt(Carbon::parse($from))) {
                    $validator->errors()->add('valid_until', 'The valid until date must not be before valid from.');
                }
            },
        ];
    }
}
