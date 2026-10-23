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

    public function view(User $user, Availability $availability): bool
    {
        return $user->doctor !== null
            && $user->doctor->id === $availability->doctor_id;
    }

    public function update(User $user, Availability $availability): bool
    {
        return $this->view($user, $availability);
    }

    public function delete(User $user, Availability $availability): bool
    {
        return $this->view($user, $availability);
    }
}
