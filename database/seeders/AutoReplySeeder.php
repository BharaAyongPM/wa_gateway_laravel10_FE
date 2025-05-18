<?php

namespace Database\Seeders;

use App\Models\AutoReply;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AutoReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AutoReply::create([
            'keyword' => 'halo',
            'response' => 'Hai juga! 👋',
            'type' => 'text',
        ]);
    }
}
