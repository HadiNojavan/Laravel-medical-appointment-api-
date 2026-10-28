<?php

namespace App\Http\Requests\Patient;

use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ListSlotsRequest extends FormRequest
{
    //user max range of asking for doctor slots can be 31days
    private const MAX_SPAN_DAYS = 31;

    //max distance from today
    private const MAX_HORIZON_DAYS = 60;

    public function authorize(): bool
    {
        return $this->user()->can('browse', Doctor::class);
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:from',
                'before_or_equal:'.now()->addDays(self::MAX_HORIZON_DAYS)->toDateString(),
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $from = Carbon::parse($this->input('from'));
                $to = Carbon::parse($this->input('to'));

                if ($to->gt($from->addDays(self::MAX_SPAN_DAYS - 1))) {
                    $validator->errors()->add('to', 'The range may not be longer than '.self::MAX_SPAN_DAYS.' days.');
                }
            },
        ];
    }
}
