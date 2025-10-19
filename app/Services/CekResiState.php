<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CekResiState
{
    private function key(string $device, string $chatId, string $suffix = ''): string
    {
        $base = "cekresi:{$device}:{$chatId}";
        return $suffix ? "{$base}:{$suffix}" : $base;
    }

    public function ttl(): int
    {
        return (int) config('cekresi_interactive.ttl_minutes', 15) * 60;
    }

    public function isActive(string $device, string $chatId): bool
    {
        return Cache::has($this->key($device, $chatId, 'state'));
    }

    public function getState(string $device, string $chatId, string $default = 'idle'): string
    {
        return Cache::get($this->key($device, $chatId, 'state'), $default);
    }

    public function getCtx(string $device, string $chatId): array
    {
        return (array) Cache::get($this->key($device, $chatId, 'ctx'), []);
    }

    public function put(string $device, string $chatId, string $state, array $ctx = []): void
    {
        $ttl = $this->ttl();
        Cache::put($this->key($device, $chatId, 'state'), $state, $ttl);
        Cache::put($this->key($device, $chatId, 'ctx'), $ctx, $ttl);
    }

    public function touch(string $device, string $chatId): void
    {
        // extend TTL dengan re-put value existing
        $this->put($device, $chatId, $this->getState($device, $chatId), $this->getCtx($device, $chatId));
    }

    public function clear(string $device, string $chatId): void
    {
        Cache::forget($this->key($device, $chatId, 'state'));
        Cache::forget($this->key($device, $chatId, 'ctx'));
    }
}
