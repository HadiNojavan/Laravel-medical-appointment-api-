<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=>User::factory()->doctor(),
            'specialty_id'=>Specialty::query()->inRandomOrder()->first()?->id
                ?? Specialty::factory(),
            'medical_license_number'=>fake()->unique()->numerify('####'),
            'is_active'=>true,
            'bio'=>$this->faker->paragraph(),

        ];
    }
}
