<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BotFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['key' => 'bantuan', 'name' => 'Menu Bantuan'],
            ['key' => 'domain_check', 'name' => 'Cek Domain'],
            ['key' => 'muslim_ai', 'name' => 'Ustad / Muslim AI'],
            ['key' => 'pinterest', 'name' => 'Pinterest'],
            ['key' => 'gpt', 'name' => 'GPT (Linda)'],
            ['key' => 'gpt_stop', 'name' => 'Stop GPT'],
            ['key' => 'gpt_pacar', 'name' => 'GPT Pacar'],
            ['key' => 'gpt_pacar_stop', 'name' => 'Stop GPT Pacar'],
            ['key' => 'renungan', 'name' => 'Renungan Islam'],
            ['key' => 'jadwal_solat', 'name' => 'Jadwal Salat'],
            ['key' => 'cek_website', 'name' => 'Cek Website'],
            ['key' => 'info_cuaca', 'name' => 'Info Cuaca BMKG'],
            ['key' => 'tiktok', 'name' => 'Download TikTok'],
            ['key' => 'animebrat', 'name' => 'Anime Brat (animeteks)'],
            ['key' => 'twitter_video', 'name' => 'Download Twitter Video'],
            ['key' => 'harga_emas', 'name' => 'Cek Harga Emas'],
            ['key' => 'resi_shopee', 'name' => 'Cek Resi Shopee'],
            ['key' => 'fakta', 'name' => 'Fakta'],
            ['key' => 'pddikti', 'name' => 'PDDIKTI'],
            ['key' => 'quote', 'name' => 'Quote'],
        ];
        foreach ($features as $f) {
            DB::table('bot_features')->updateOrInsert(['key' => $f['key']], $f);
        }
    }
}
