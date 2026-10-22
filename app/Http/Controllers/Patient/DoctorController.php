<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\ListActiveDoctorsRequest;
use App\Http\Resources\PublicDoctorResource;
use App\Services\DoctorService;

class DoctorController extends Controller
{
    public function index(ListActiveDoctorsRequest $request, DoctorService $doctorService)
    {
        $doctors = $doctorService->listActive($request->validated());

        return PublicDoctorResource::collection($doctors);
    }
}
