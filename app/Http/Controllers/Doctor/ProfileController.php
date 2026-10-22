<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorProfileRequest;
use App\Http\Resources\DoctorResource;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $doctor = $request->user()->doctor;

        abort_if($doctor === null, 403, 'Only doctors have a profile.');

        Gate::authorize('view', $doctor);

        return new DoctorResource($doctor->load('user', 'specialty'));
    }

    public function update(UpdateDoctorProfileRequest $request, DoctorService $doctorService)
    {
        $doctor = $doctorService->update(
            $request->user()->doctor,
            $request->validated()
        );

        return new DoctorResource($doctor);
    }
}
