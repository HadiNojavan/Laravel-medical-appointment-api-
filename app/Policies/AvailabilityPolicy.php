<?php

namespace App\Policies;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AvailabilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDoctor() && $user->doctor !== null;
    }

    public function create(User $user): bool
    {
        return $user->isDoctor() && $user->doctor !== null;
    }
}
