<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nCallbackController extends Controller
{
    public function handle(Request $req)
    {
        // Verifikasi token sederhana (cukup untuk tahap awal; nanti bisa upgrade HMAC)
        $token = $req->header('X-Callback-Token') ?? data_get($req->input('auth'), 'token');
        if ($token !== config('services.n8n.callback_token')) {
            Log::warning('n8n_callback_invalid_token');
            return response()->json(['ok' => false, 'err' => 'invalid token'], 401);
        }

        $actions = $req->input('actions', []);
        if (!is_array($actions) || empty($actions)) {
            return response()->json(['ok' => false, 'err' => 'no actions']);
        }

        foreach ($actions as $action) {
            try {
                $this->dispatchAction($action);
            } catch (\Throwable $e) {
                Log::error('n8n_action_failed', ['action' => $action, 'err' => $e->getMessage()]);
            }
        }

        return response()->json(['ok' => true, 'delivered' => count($actions)]);
    }

    protected function dispatchAction(array $action)
    {
        $base = config('services.wago.base_url', env('WA_GO_BASE', 'http://wa-go:3000'));
        $type = $action['type'] ?? 'send_text';

        switch ($type) {
            case 'send_text':
                Http::post("{$base}/send-text", [
                    'to'   => $action['to'],
                    'text' => $action['message'],
                ]);
                break;

            case 'send_media':
                Http::post("{$base}/send-media", [
                    'to'      => $action['to'],
                    'url'     => $action['mediaUrl'],
                    'caption' => $action['caption'] ?? null,
                ]);
                break;

                // Tambahin varian lain: send_sticker, react, reply_to, forward_to, dll.
        }
    }
}
