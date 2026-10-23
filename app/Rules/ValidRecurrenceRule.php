<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;
use RRule\RRule;


/*
 * here we validate doctor rrule
 * here is the rules
 * no new lines and no :
 * parts should separated by :
 * FREQ must be WEEKLY
 *BYDAY is required unique days MO,TU,WE,TH,FR,SA,SU
 * INTERVAL is optional . a number from 1 to 4.
 * WKST [week start] is optional. one valid day
 * finally we check that our rrule can parse it
 */
class ValidRecurrenceRule implements ValidationRule
{
    private const ALLOWED_PARTS = ['FREQ', 'BYDAY', 'INTERVAL', 'WKST'];

    private const DAYS = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '' || preg_match('/[\r\n:]/', $value)) {
            $fail('The :attribute must be a single RRULE line without DTSTART.');
            return;
        }

        $parts = [];

        foreach (explode(';', $value) as $segment) {
            $pair = explode('=', $segment, 2);

            if (count($pair) !== 2 || $pair[0] === '' || $pair[1] === '') {
                $fail('The :attribute is malformed.');
                return;
            }

            [$key, $val] = $pair;

            if (! in_array($key, self::ALLOWED_PARTS, true)) {
                $fail("The :attribute part {$key} is not allowed.");
                return;
            }

            if (array_key_exists($key, $parts)) {
                $fail("The :attribute part {$key} is repeated.");
                return;
            }

            $parts[$key] = $val;
        }

        if (($parts['FREQ'] ?? null) !== 'WEEKLY') {
            $fail('The :attribute must contain FREQ=WEEKLY.');
            return;
        }

        if (! isset($parts['BYDAY'])) {
            $fail('The :attribute must contain BYDAY.');
            return;
        }

        $days = explode(',', $parts['BYDAY']);

        if (count($days) !== count(array_unique($days)) || array_diff($days, self::DAYS) !== []) {
            $fail('BYDAY must list unique days from MO, TU, WE, TH, FR, SA, SU.');
            return;
        }

        if (isset($parts['INTERVAL']) && ! preg_match('/^[1-4]$/', $parts['INTERVAL'])) {
            $fail('INTERVAL must be a number between 1 and 4.');
            return;
        }

        if (isset($parts['WKST']) && ! in_array($parts['WKST'], self::DAYS, true)) {
            $fail('WKST must be a valid day code.');
            return;
        }

        try {
            new RRule("DTSTART:20260101\nRRULE:{$value}");
        } catch (InvalidArgumentException) {
            $fail('The :attribute is not a valid recurrence rule.');
        }
    }
}
