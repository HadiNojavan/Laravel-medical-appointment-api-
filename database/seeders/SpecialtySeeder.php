<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties =[
            'Cardiology', 'Dermatology', 'Neurology', 'Pediatrics',
            'Orthopedics', 'Psychiatry', 'Radiology', 'Oncology',
        ];
        foreach ($specialties as $specialty) {
            Specialty::factory()->create(['name' => $specialty]);
        }
    }
}
