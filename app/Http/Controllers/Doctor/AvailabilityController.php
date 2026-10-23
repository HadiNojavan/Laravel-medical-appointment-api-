<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreAvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use App\Models\Availability;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Availability::class);

        $availabilities = $request->user()->doctor
            ->availabilities()
            ->latest()
            ->get();

        return AvailabilityResource::collection($availabilities);
    }

    public function store(StoreAvailabilityRequest $request, AvailabilityService $service)
    {
        $availability = $service->create(
            $request->user()->doctor,
            $request->validated()
        );

        return (new AvailabilityResource($availability))
            ->response()
            ->setStatusCode(201);
    }
}
