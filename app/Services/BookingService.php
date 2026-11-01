<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

class BookingService
{
    private const MAX_HORIZON_DAYS = 60;

    public function __construct(private SlotService $slotService)
    {
    }

    public function book(User $patient, Doctor $doctor, string $startAt): Appointment
    {
        $start = CarbonImmutable::parse($startAt)->utc();

        if ($start->greaterThan(now()->addDays(self::MAX_HORIZON_DAYS))) {
            throw ValidationException::withMessages([
                'start_at' => ['Appointments can be booked at most '.self::MAX_HORIZON_DAYS.' days ahead.'],
            ]);
        }


        $slot = $this->findFreeSlot($doctor, $start);

        if ($slot === null) {
            throw ValidationException::withMessages([
                'start_at' => ['This time is not an available slot.'],
            ]);
        }

        $appointment = $doctor->appointments()->create([
            'patient_id' => $patient->id,
            'start_at' => $start,
            'end_at' => CarbonImmutable::parse($slot['end_at'])->utc(),
        ]);

        return $appointment->load('doctor.user', 'doctor.specialty');
    }

    private function findFreeSlot(Doctor $doctor, CarbonImmutable $start): ?array
    {
        // one day of margin on each side, because "the day" depends on the availability timezone
        $slots = $this->slotService->forDoctor(
            $doctor,
            $start->subDay()->toDateString(),
            $start->addDay()->toDateString(),
        );

        return $slots->firstWhere('start_at', $start->toIso8601ZuluString());
    }
}
