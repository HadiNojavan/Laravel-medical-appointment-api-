<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\BookAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Doctor;
use App\Services\BookingService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(BookAppointmentRequest $request, BookingService $bookingService)
    {
        $doctor = Doctor::findOrFail($request->validated('doctor_id'));

        abort_unless($doctor->is_active, 404);

        $appointment = $bookingService->book(
            $request->user(),
            $doctor,
            $request->validated('start_at'),
        );

        return (new AppointmentResource($appointment))
            ->response()
            ->setStatusCode(201);
    }
}
