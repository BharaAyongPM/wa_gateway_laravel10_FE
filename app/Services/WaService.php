<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

class WaService
{
    protected string $base;
    protected array $ep;        // <- gunakan ini konsisten
    protected string $token;
    protected int $timeout;

    public function __construct()
    {
        $this->base    = rtrim(config('wa.base_url'), '/');
        $this->ep      = (array) config('wa.endpoints', []);
        $this->token   = (string) env('WA_API_TOKEN', '');
        $this->timeout = (int) config('wa.timeout', 15);
    }

    protected function http()
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->timeout($this->timeout);
    }

    /** Resolve path dari key endpoint, plus alias & {session} replacement */
    protected function resolvePath(string $key, array $payloadOrQuery = []): string
    {
        // alias supaya backward/forward compatible
        $aliases = [
            'list_groups'      => 'groups',
            'send_media_buffer' => 'send_media',
            'send-message'      => 'send_message',
        ];

        $lookupKey = $this->ep[$key] ?? null ? $key
            : (isset($aliases[$key]) ? $aliases[$key] : $key);

        $path = $this->ep[$lookupKey] ?? null;
        if (!$path) {
            throw new InvalidArgumentException("Unknown endpoint key: {$key}");
        }

        // inject {session} kalau ada
        if (Str::contains($path, '{session}')) {
            $session = $payloadOrQuery['session']
                ?? $payloadOrQuery['device']
                ?? null;
            $path = str_replace('{session}', urlencode((string) $session), $path);
        }
        return $path;
    }

    // ================== Device ==================
    public function createDevice(string $sessionId)
    {
        $path = $this->resolvePath('create', ['id' => $sessionId]);
        return $this->http()->post($this->base . $path, ['id' => $sessionId]);
    }

    public function getStatus(string $sessionId)
    {
        $path = $this->resolvePath('status', ['session' => $sessionId]);
        return $this->http()->get($this->base . $path);
    }

    public function getQr(string $sessionId)
    {
        $path = $this->resolvePath('qr', ['session' => $sessionId]);
        return $this->http()->get($this->base . $path);
    }

    public function deleteDevice(string $sessionId)
    {
        $path = $this->resolvePath('delete', ['session' => $sessionId]);
        return $this->http()->delete($this->base . $path);
    }

    // ================== Messaging ==================
    public function sendMessage(array $payload)
    {
        // server Go expects: { device, to, text }
        $path = $this->resolvePath('send_message', $payload);

        $body = [
            'device' => $payload['session'] ?? $payload['device'] ?? '',
            'to'     => (string) ($payload['to'] ?? ''),
            'text'   => (string) ($payload['text'] ?? ($payload['caption'] ?? '')),
        ];

        return $this->http()->post($this->base . $path, $body);
    }

    public function sendMediaBase64(string $session, string $to, string $base64, string $caption = '', ?string $filename = null, ?string $mimetype = null, ?string $mediaType = null)
    {
        $path = $this->resolvePath('send_media', ['device' => $session, 'to' => $to]);

        $body = array_filter([
            'device'     => $session,
            'to'         => $to,
            'base64'     => $base64,
            'caption'    => $caption,
            'filename'   => $filename,
            'mimetype'   => $mimetype,
            'media_type' => $mediaType, // hint opsional
        ], fn($v) => $v !== null && $v !== '');

        return $this->http()->post($this->base . $path, $body);
    }


    public function sendMediaFromUrl(string $session, string $to, string $url, string $caption = '')
    {
        $bin = @file_get_contents($url);
        if ($bin === false) {
            Log::warning('sendMediaFromUrl download failed', ['url' => $url]);
            return Http::response(['ok' => false, 'error' => 'Download failed'], 400);
        }
        return $this->sendMediaBase64($session, $to, base64_encode($bin), $caption);
    }

    public function sendMediaBuffer(string $sessionId, string $fullPath, string $filename, array $meta = [])
    {
        if (!is_file($fullPath) || !is_readable($fullPath)) {
            Log::warning('sendMediaBuffer file not readable', ['path' => $fullPath]);
            return Http::response(['ok' => false, 'error' => 'File not readable'], 400);
        }

        $bin = @file_get_contents($fullPath);
        if ($bin === false) {
            Log::warning('sendMediaBuffer read failed', ['path' => $fullPath]);
            return Http::response(['ok' => false, 'error' => 'File read failed'], 500);
        }

        $b64   = base64_encode($bin);
        $to    = (string) ($meta['to'] ?? '');
        $cap   = (string) ($meta['caption'] ?? '');

        return $this->sendMediaBase64($sessionId, $to, $b64, $cap);
    }

    // ================== Generic POST/GET by key ==================
    public function postTo(string $key, array $payload = [])
    {
        // normalisasi khusus key yang kita tahu strukturnya
        if ($key === 'send_message') {
            return $this->sendMessage($payload);
        }
        if ($key === 'send_media_buffer') {
            $session = (string) ($payload['session'] ?? '');
            $to      = (string) ($payload['to'] ?? '');
            $b64     = (string) ($payload['base64'] ?? ($payload['data'] ?? ''));
            $caption = (string) ($payload['caption'] ?? '');
            if ($session && $to && $b64) {
                return $this->sendMediaBase64($session, $to, $b64, $caption);
            }
            return Http::response(['ok' => false, 'error' => 'invalid media payload'], 400);
        }

        // sisanya: pakai path dari config
        $path = $this->resolvePath($key, $payload);
        return $this->http()->post($this->base . $path, $payload);
    }

    public function getTo(string $key, array $query = [])
    {
        $path = $this->resolvePath($key, $query);
        return $this->http()->get($this->base . $path, $query);
    }

    // ================== Groups ==================
    public function getGroups(string $sessionId, bool $withParticipants = false)
    {
        return $this->getTo('list_groups', [
            'session'              => $sessionId,
            'include_participants' => $withParticipants ? '1' : '0',
        ]);
    }
}
