<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\BookAppointmentRequest;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(BookAppointmentRequest $request)
    {
        // temporary: BookingService comes in the next step
        return response()->json(['data' => $request->validated()]);
    }
}
