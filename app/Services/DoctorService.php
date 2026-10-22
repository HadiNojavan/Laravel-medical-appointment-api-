<?php

namespace App\Services;

use App\Enums\RoleUser;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class DoctorService
{
    /*
     * here if for example after creating doctor error happens
     * we will rollback and no doctor will be created because we use transaction
     * if no error happens from start to end  it will commit our transaction in database
     */
    public function create(array $data): Doctor
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                ...Arr::only($data, ['name', 'email', 'password']),
                'role' => RoleUser::Doctor,
            ]);

            $doctor = $user->doctor()->create(
                Arr::only($data, ['specialty_id', 'medical_license_number', 'bio'])
            );


            return $doctor->load('user', 'specialty');
        });
    }
}
