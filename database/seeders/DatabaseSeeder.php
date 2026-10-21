<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([SpecialtySeeder::class]);

        User::factory()->admin()->create([
            'name' => 'hadi',
            'email'=>'hadi@gmail.com',
            'password' => bcrypt('hadi123'),
        ]);
        $doctorUser = User::factory()->doctor()->create([
            'name' => 'doctor brown',
            'email' => 'doctor@gmail.com',
            'password' => bcrypt('doctor123'),
        ]);

        Doctor::factory()->create(['user_id' => $doctorUser->id]);

        Doctor::factory()->count(3)->create();

        User::factory()->count(10)->create();

    }
}
