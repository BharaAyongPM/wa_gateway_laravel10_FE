<?php


namespace App\Http\Controllers;

use App\Models\AutoReply;
use App\Models\Device;
use App\Models\DeviceBot;
use App\Models\DeviceBotFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MessagesController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        $devices = Device::query()
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->with('activeSubscription.plan')
            ->orderByDesc('id')
            ->get();

        // kirim flag admin ke view biar JS/Blade bisa buka fitur
        return view('messages.index', [
            'devices' => $devices,
            'isAdmin' => $isAdmin,
        ]);
    }

    // Ambil daftar grup langsung dari server WA berdasarkan session device
    public function groupsByDevice(Request $request, \App\Services\WaService $wa)
    {
        $request->validate([
            'device_id' => 'required|integer|exists:devices,id',
        ]);

        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        $device = \App\Models\Device::query()
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->findOrFail($request->device_id);

        // set true kalau mau hitung member (lebih lambat)
        $res = $wa->getGroups($device->session_id, false);

        if (!$res->ok()) {
            return response()->json([
                'success' => false,
                'error'   => 'Tidak bisa menghubungi server WA (HTTP ' . $res->status() . ')',
            ], 502);
        }

        $json = $res->json() ?? [];
        $success = $json['success'] ?? $json['ok'] ?? ($res->status() === 200);
        if (!$success) {
            return response()->json([
                'success' => false,
                'error'   => $json['error'] ?? 'Gagal memuat grup dari server WA.',
            ], 500);
        }

        $rawGroups = $json['groups'] ?? $json['data'] ?? [];
        $groups = [];
        foreach ((array)$rawGroups as $g) {
            $wid  = $g['wid']  ?? $g['jid'] ?? $g['id'] ?? null;
            $name = $g['name'] ?? $g['subject'] ?? $g['title'] ?? '(tanpa nama)';
            $pc   = $g['participants_count'] ?? $g['size'] ?? 0;

            if (!$wid && isset($g['id']) && is_string($g['id']) && preg_match('~^\d+$~', $g['id'])) {
                $wid = $g['id'] . '@g.us';
            }
            if ($wid) {
                if (!str_ends_with($wid, '@g.us') && preg_match('~^\d+$~', $wid)) {
                    $wid .= '@g.us';
                }
                $groups[] = [
                    'wid' => $wid,
                    'name' => $name,
                    'participants_count' => (int)$pc,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'groups'  => $groups,
        ]);
    }

    public function store(Request $request, \App\Services\WaService $wa)
    {
        $request->validate([
            'device_id'   => 'required|integer|exists:devices,id',
            'target_type' => 'required|in:numbers,groups',
            'message'     => 'required|string|max:10000',
            'numbers'     => 'nullable|string',     // CSV 628xxx,628yyy
            'groups'      => 'nullable|array',      // array WID grup (120...@g.us)
            'groups.*'    => 'string',
            'attachment'  => 'nullable|file|max:10240',
        ]);

        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';

        $device = Device::query()
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->with('activeSubscription.plan')
            ->findOrFail($request->device_id);

        $sessionId = $device->session_id;

        // --- Attachment policy by plan (ADMIN bypass) ---
        $sub = $device->activeSubscription;
        $allowAttachmentPlan = $sub && !$sub->is_trial && (bool) optional($sub->plan)->allow_attachment;
        $allowAttachment = $isAdmin ? true : $allowAttachmentPlan;
        $maxMb = $isAdmin
            ? 100 // admin: kasih batas besar
            : (int) (optional($sub->plan)->max_attachment_mb ?? 10);

        $attachmentPath = null;
        $mediaUrl = null;

        if ($request->hasFile('attachment')) {
            if (!$allowAttachment) {
                return response()->json(['success' => false, 'message' => 'Plan Anda tidak mengizinkan pengiriman lampiran.'], 422);
            }
            $sizeMb = $request->file('attachment')->getSize() / (1024 * 1024);
            if ($sizeMb > $maxMb) {
                return response()->json(['success' => false, 'message' => "Ukuran file melebihi batas ({$maxMb} MB)."], 422);
            }
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            // kirim sebagai URL pesan kedua (server WA ada yang minta media buffer — tinggal ganti di sini jika mau buffer)
            $mediaUrl = Storage::disk('public')->url($attachmentPath);
        }

        // --- Normalisasi target ---
        $isGroup = $request->target_type === 'groups';
        if ($isGroup) {
            $targets = collect($request->groups ?: [])
                ->map(fn($g) => trim($g))
                ->filter()
                ->map(function ($wid) {
                    // pastikan wid grup valid
                    if (!str_ends_with($wid, '@g.us')) {
                        // toleransi kalau ada yang kirim numerik
                        if (preg_match('~^\d+$~', $wid)) return $wid . '@g.us';
                    }
                    return $wid;
                })
                ->values()
                ->all();

            if (empty($targets)) {
                return response()->json(['success' => false, 'message' => 'Pilih minimal satu grup.'], 422);
            }
        } else {
            $targets = collect(explode(',', (string) $request->numbers))
                ->map(fn($s) => trim($s))
                ->filter()
                ->map(fn($n) => preg_replace('/[^0-9]/', '', $n))
                ->filter()
                ->map(fn($n) => $n . '@c.us')
                ->values()
                ->all();

            if (empty($targets)) {
                return response()->json(['success' => false, 'message' => 'Daftar nomor kosong/tidak valid.'], 422);
            }
        }

        // --- Kurangi kuota (admin bypass? tetap dihitung tapi boleh diset non-kritis) ---
        $attempts = count($targets);
        if ($attempts > 0) {
            DB::transaction(function () use ($device, $attempts) {
                $active = $device->activeSubscription()->lockForUpdate()->first();
                if ($active) {
                    $active->remaining_quota = max(0, (int)$active->remaining_quota - $attempts);
                    $active->save();
                }
                $device->increment('messages_sent', $attempts);
            });
        }

        // Watermark untuk trial (admin tetap no watermark)
        $baseMessage = $request->message;
        if (!$isAdmin && $device->is_trial) {
            $baseMessage .= "\n\n*_" . 'Send by wa.ziezie.my.id' . "_*";
        }

        $results = [];
        $ok = 0;
        $fail = 0;

        foreach ($targets as $target) {
            $to = $isGroup ? $target : ($target . '@c.us');
            $recipientType = $isGroup ? 'group' : 'number';

            try {
                $success = false;
                $messageId = null;
                $errorMsg = null;

                // Tentukan caption & tipe media (kalau ada file)
                $caption = $baseMessage; // biar jadi caption media
                $mt = 'text';
                $filename = null;
                $mimetype = null;
                $b64 = null;
                $mediaTypeHint = null;

                if ($attachmentPath) {
                    $fullPath = Storage::disk('public')->path($attachmentPath);
                    $filename = basename($fullPath);
                    $mimetype = $request->file('attachment')->getMimeType(); // lebih akurat
                    $bin = @file_get_contents($fullPath);
                    if ($bin === false) {
                        throw new \RuntimeException('Gagal baca file lampiran');
                    }
                    $b64 = base64_encode($bin);

                    // map ke message_type (sesuai ENUM di DB kamu)
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    if (str_starts_with($mimetype, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $mt = 'image';
                        $mediaTypeHint = 'image';
                    } elseif (str_starts_with($mimetype, 'video/') || in_array($ext, ['mp4', 'mov', 'mkv', 'avi'])) {
                        $mt = 'video';
                        $mediaTypeHint = 'video';
                    } elseif (str_starts_with($mimetype, 'audio/') || in_array($ext, ['mp3', 'm4a', 'aac', 'ogg', 'opus'])) {
                        $mt = 'audio';
                        $mediaTypeHint = 'audio';
                    } else {
                        $mt = 'document';
                        $mediaTypeHint = 'document';
                    }
                }

                // Kirim:
                if ($b64) {
                    // Kirim media dengan caption
                    $res = $wa->sendMediaBase64($sessionId, $to, $b64, $caption, $filename, $mimetype, $mediaTypeHint);
                } else {
                    // Tanpa file → kirim teks biasa
                    $res = $wa->sendMessage([
                        'session' => $sessionId,
                        'to'      => $to,
                        'text'    => $baseMessage,
                    ]);
                }

                $success   = $res->ok() && ($res->json('ok') === true || $res->json('success') === true);
                $messageId = data_get($res->json(), 'id.ID') ?? data_get($res->json(), 'messageId');

                \App\Models\Message::create([
                    'user_id'         => auth()->id(),
                    'device_id'       => $device->id,
                    'sender'          => $device->name,
                    'recipient'       => $target,
                    'recipient_type'  => $recipientType,
                    'message_type'    => $mt,                       // <-- ENUM friendly
                    'content'         => $baseMessage,              // teks/legend
                    'media_url'       => null,                      // gak perlu URL publik lagi
                    'status'          => $success ? 'sent' : 'failed',
                    'provider'        => 'wa-webjs',
                    'error_message'   => $success ? null : ($res->json('error') ?? 'send failed'),
                    'retry_count'     => 0,
                    'scheduled_at'    => null,
                    'sent_at'         => $success ? now() : null,
                    'meta'            => [
                        'node_response' => $res->json(),
                        'message_id'    => $messageId,
                        'device_session' => $sessionId,
                        'filename'      => $filename,
                        'mimetype'      => $mimetype,
                    ],
                ]);

                $results[] = [
                    'to'        => $target,
                    'status'    => $success ? 'OK' : 'FAIL',
                    'messageId' => $messageId,
                ];
                $success ? $ok++ : $fail++;
            } catch (\Throwable $e) {
                $fail++;
                \App\Models\Message::create([
                    'user_id'         => auth()->id(),
                    'device_id'       => $device->id,
                    'sender'          => $device->name,
                    'recipient'       => $target,
                    'recipient_type'  => $recipientType,
                    'message_type'    => $attachmentPath ? ($mt ?? 'document') : 'text',
                    'content'         => $baseMessage,
                    'media_url'       => null,
                    'status'          => 'failed',
                    'provider'        => 'wa-webjs',
                    'error_message'   => $e->getMessage(),
                    'retry_count'     => 0,
                    'scheduled_at'    => null,
                    'sent_at'         => null,
                    'meta'            => ['exception' => true],
                ]);

                $results[] = ['to' => $target, 'status' => 'FAIL', 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'success' => $fail === 0,
            'summary' => "Terkirim: {$ok}, Gagal: {$fail}",
            'results' => $results,
        ]);
    }
}
