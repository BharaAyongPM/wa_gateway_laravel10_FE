<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LindaService
{
    protected string $base = 'https://zaikyoov3.koyeb.app/api/o3-mini';

    public function ask(string $prompt, string $session = '1'): ?string
    {
        $resp = Http::timeout(20)->get($this->base, [
            'prompt'  => $prompt,
            'session' => $session,
        ]);

        if ($resp->successful()) {
            $json = $resp->json();
            return $json['reply'] ?? null;
        }
        return null;
    }
}
