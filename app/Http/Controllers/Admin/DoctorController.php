<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexDoctorRequest;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Requests\UpdateDoctorStatusRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DoctorController extends Controller
{
    public function index(IndexDoctorRequest $request, DoctorService $doctorService)
    {
        $doctors = $doctorService->list($request->validated());

        return DoctorResource::collection($doctors);
    }

    public function show(Doctor $doctor)
    {
        Gate::authorize('view', $doctor);

        return new DoctorResource($doctor->load('user', 'specialty'));
    }

    public function store(StoreDoctorRequest $request,DoctorService $doctorService)
    {
        //we move logic to service

        $doctor=$doctorService->create($request->validated());

        return response()->json([
            'new doctor created'=>new DoctorResource($doctor),
        ],status: 201);
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor, DoctorService $doctorService)
    {
        $doctor = $doctorService->update($doctor, $request->validated());

        return new DoctorResource($doctor);
    }

    public function updateStatus(UpdateDoctorStatusRequest $request, Doctor $doctor, DoctorService $doctorService)
    {
        $doctor = $doctorService->updateStatus($doctor, $request->boolean('is_active'));
        return new DoctorResource($doctor);
    }
}
