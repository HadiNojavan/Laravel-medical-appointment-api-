<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexDoctorRequest;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Services\DoctorService;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(IndexDoctorRequest $request, DoctorService $doctorService)
    {
        $doctors = $doctorService->list($request->validated());

        return DoctorResource::collection($doctors);
    }

    public function store(StoreDoctorRequest $request,DoctorService $doctorService)
    {
        //we move logic to service

        $doctor=$doctorService->create($request->validated());

        return response()->json([
            'new doctor created'=>new DoctorResource($doctor),
        ],status: 201);
    }
}
