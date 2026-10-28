<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\ListActiveDoctorsRequest;
use App\Http\Requests\Patient\ListSlotsRequest;
use App\Http\Resources\PublicDoctorResource;
use App\Models\Doctor;
use App\Services\DoctorService;
use App\Services\SlotService;

class DoctorController extends Controller
{
    public function index(ListActiveDoctorsRequest $request, DoctorService $doctorService)
    {
        $doctors = $doctorService->listActive($request->validated());

        return PublicDoctorResource::collection($doctors);
    }

    public function slots(ListSlotsRequest $request, Doctor $doctor, SlotService $slotService)
    {
        abort_unless($doctor->is_active, 404);

        return response()->json([
            'data' => $slotService->forDoctor(
                $doctor,
                $request->validated('from'),
                $request->validated('to'),
            ),
        ]);
    }
}
