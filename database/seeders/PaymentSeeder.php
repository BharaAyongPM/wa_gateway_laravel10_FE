<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payment::create([
            'user_id' => 2,
            'plan_id' => 1, // pastikan plan_id 1 sudah ada, misal untuk paket Basic
            'transaction_id' => Str::uuid(),
            'status' => 'success',
            'amount' => 40000,
            'payment_type' => 'manual',
            'paid_at' => now()->subDays(2),
        ]);
    }
}
