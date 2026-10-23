<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RRule\RRule;


/*
     * here when we want to create new Availability for a doctor
     * first we have to check if there is conflict with last Availability that
     * doctor has been created
*/
class AvailabilityService
{
    // INTERVAL is limited to 1..4, so the combined pattern of two rules
    // repeats at most every interval(1..4) = 12 weeks. 84 days covers one full cycle.

    private const int LOOKAHEAD_DAYS = 84;


    public function create(Doctor $doctor, array $data): Availability
    {
        return DB::transaction(function () use ($doctor, $data) {
            Doctor::whereKey($doctor->id)->lockForUpdate()->first();

            $this->assertNoConflict($doctor, new Availability($data));

            return $doctor->availabilities()->create($data);
        });
    }

    public function update(Availability $availability, array $data): Availability
    {
        return DB::transaction(function () use ($availability, $data) {
            $doctor = $availability->doctor;

            /*
             * lock for update: we lock doctor id table
             * so if in this time doctor again send new request
             * it will be wait untill this process ends
             */

            Doctor::whereKey($doctor->id)->lockForUpdate()->first();

            // what the record would look like after the update
            $candidate = $availability->replicate()->fill($data);

            //now we check if we have conflicts with last Availability
            if ($candidate->is_active) {
                $this->assertNoConflict($doctor, $candidate, ignoreId: $availability->id);
            }

            $availability->update($data);

            return $availability->refresh();
        });
    }

    private function assertNoConflict(Doctor $doctor, Availability $candidate, ?int $ignoreId = null): void
    {
        $others = $doctor->availabilities()
            ->where('is_active', true)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->get();

        foreach ($others as $other) {
            if ($this->conflicts($candidate, $other)) {
                throw ValidationException::withMessages([
                    'start_time' => ['This schedule overlaps with an existing availability.'],
                ]);
            }
        }
    }
    private function conflicts(Availability $a, Availability $b): bool
    {
        // 1. the period where both rules are valid
        $from = $a->valid_from->greaterThan($b->valid_from) ? $a->valid_from : $b->valid_from;
        $from = CarbonImmutable::parse($from->toDateString());
        $to = $from->addDays(self::LOOKAHEAD_DAYS);

        foreach ([$a, $b] as $item) {
            if ($item->valid_until !== null) {
                $until = CarbonImmutable::parse($item->valid_until->toDateString());

                if ($until->lessThan($to)) {
                    $to = $until;
                }
            }
        }

        if ($from->greaterThan($to)) {
            return false;
        }

        // expand both rules into concrete UTC intervals
        $aIntervals = $this->intervals($a, $from, $to);
        $bIntervals = $this->intervals($b, $from, $to);

        //any overlap between the two lists?
        foreach ($aIntervals as [$aStart, $aEnd]) {
            foreach ($bIntervals as [$bStart, $bEnd]) {
                if ($aStart < $bEnd && $aEnd > $bStart) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<int, array{0: CarbonImmutable, 1: CarbonImmutable}>
     */
    private function intervals(Availability $availability, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $tz = $availability->timezone;

        $rule = new RRule(sprintf(
            "DTSTART;TZID=%s:%sT000000\nRRULE:%s",
            $tz,
            $availability->valid_from->format('Ymd'),
            $availability->rrule
        ));

        // one second before midnight so an occurrence exactly at 00:00 is never skipped
        $begin = (new DateTime($from->toDateString().' 00:00:00', new DateTimeZone($tz)))
            ->modify('-1 second');
        $end = new DateTime($to->toDateString().' 23:59:59', new DateTimeZone($tz));

        $intervals = [];

        foreach ($rule->getOccurrencesBetween($begin, $end) as $occurrence) {
            $day = $occurrence->format('Y-m-d');

            $intervals[] = [
                CarbonImmutable::parse("$day {$availability->start_time}", $tz)->utc(),
                CarbonImmutable::parse("$day {$availability->end_time}", $tz)->utc(),
            ];
        }
        return $intervals;
    }
}
