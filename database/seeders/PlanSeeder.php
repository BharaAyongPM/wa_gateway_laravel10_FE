<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::insert([
            [
                'name' => 'Trial',
                'price' => 0,
                'duration' => 5,
                'max_devices' => 1,
                'quota_limit' => null,
                'can_image' => false,
                'can_pdf' => false,
                'can_autoreply' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Pesan Saja',
                'price' => 40000,
                'duration' => 30,
                'max_devices' => 3,
                'quota_limit' => 1000,
                'can_image' => false,
                'can_pdf' => false,
                'can_autoreply' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Media',
                'price' => 75000,
                'duration' => 30,
                'max_devices' => 5,
                'quota_limit' => null,
                'can_image' => true,
                'can_pdf' => true,
                'can_autoreply' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Lengkap',
                'price' => 100000,
                'duration' => 30,
                'max_devices' => 10,
                'quota_limit' => null,
                'can_image' => true,
                'can_pdf' => true,
                'can_autoreply' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
