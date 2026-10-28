<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Collection;
use RRule\RRule;

class SlotService
{
    private const MIN_NOTICE_MINUTES = 60;

    /**
     * @return Collection<int, array{start_at: string, end_at: string}>
     */
    public function forDoctor(Doctor $doctor, string $from, string $to): Collection
    {
        // 1. only availabilities whose validity touches the requested window
        $availabilities = $doctor->availabilities()
            ->where('is_active', true)
            ->whereDate('valid_from', '<=', $to)
            ->where(fn ($q) => $q->whereNull('valid_until')
                ->orWhereDate('valid_until', '>=', $from))
            ->get();

        // 2. expand every availability into concrete UTC slots
        $slots = collect();

        foreach ($availabilities as $availability) {
            $slots = $slots->merge($this->generate($availability, $from, $to));
        }

        // 3. drop past slots and slots that are too close to now
        $earliest = CarbonImmutable::now('UTC')->addMinutes(self::MIN_NOTICE_MINUTES);

        $slots = $slots
            ->filter(fn (array $slot) => $slot['start'] >= $earliest)
            ->unique(fn (array $slot) => $slot['start']->getTimestamp())
            ->sortBy(fn (array $slot) => $slot['start']->getTimestamp())
            ->values();

        if ($slots->isEmpty()) {
            return $slots;
        }

        // 4. remove slots that overlap a booked appointment
        $slots = $this->withoutBooked($doctor, $slots);

        // 5. (later) remove slots that overlap a leave

        return $slots->map(fn (array $slot) => [
            'start_at' => $slot['start']->toIso8601ZuluString(),
            'end_at' => $slot['end']->toIso8601ZuluString(),
        ])->values();
    }

    private function generate(Availability $availability, string $from, string $to): array
    {
        $tz = $availability->timezone;

        $rule = new RRule(sprintf(
            "DTSTART;TZID=%s:%sT000000\nRRULE:%s",
            $tz,
            $availability->valid_from->format('Ymd'),
            $availability->rrule
        ));

        $begin = (new DateTime("$from 00:00:00", new DateTimeZone($tz)))->modify('-1 second');
        $end = new DateTime("$to 23:59:59", new DateTimeZone($tz));

        $slots = [];

        foreach ($rule->getOccurrencesBetween($begin, $end) as $occurrence) {
            $day = $occurrence->format('Y-m-d');

            if ($availability->valid_until && $day > $availability->valid_until->toDateString()) {
                continue;
            }

            $cursor = CarbonImmutable::parse("$day {$availability->start_time}", $tz)->utc();
            $limit = CarbonImmutable::parse("$day {$availability->end_time}", $tz)->utc();

            while (($slotEnd = $cursor->addMinutes($availability->slot_duration_minutes)) <= $limit) {
                $slots[] = ['start' => $cursor, 'end' => $slotEnd];
                $cursor = $slotEnd;
            }
        }

        return $slots;
    }

    private function withoutBooked(Doctor $doctor, Collection $slots): Collection
    {
        $windowStart = $slots->first()['start'];
        $windowEnd = CarbonImmutable::createFromTimestampUTC(
            $slots->map(fn (array $slot) => $slot['end']->getTimestamp())->max()
        );

        // one query for the whole window, never a query inside a loop
        $booked = $doctor->appointments()
            ->where('status', '<>', AppointmentStatus::Cancelled->value)
            ->where('start_at', '<', $windowEnd)
            ->where('end_at', '>', $windowStart)
            ->get(['start_at', 'end_at']);

        return $slots->reject(function (array $slot) use ($booked) {
            return $booked->contains(
                fn ($appointment) => $slot['start'] < $appointment->end_at
                    && $slot['end'] > $appointment->start_at
            );
        })->values();
    }
}
