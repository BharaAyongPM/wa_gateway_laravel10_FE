<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\WaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        // Eager load subscription aktif + plan untuk menghindari N+1
        $q = Device::query();

        // User biasa hanya lihat device miliknya
        if (auth()->user()->role !== 'admin') {
            $q->where('user_id', auth()->id());
        }

        $devices = $q->with(['activeSubscription.plan'])->latest()->get();

        return view('device.index', compact('devices'));
    }

    public function create(Request $request, WaService $wa)
    {
        // tentukan pemilik device
        $ownerId = auth()->id();
        if (auth()->user()->role === 'admin' && $request->filled('user_id')) {
            $ownerId = (int) $request->input('user_id');
        }

        // Generate session ID
        $sessionId = 'device-' . Str::random(8);

        // Panggil server WA buat sesi baru
        $response = $wa->createDevice($sessionId);

        if (!$response->successful()) {
            return back()->with('error', 'Gagal membuat sesi device di server WA.');
        }

        DB::transaction(function () use ($sessionId, $ownerId) {
            $device = \App\Models\Device::create([
                'name'              => 'Device ' . strtoupper(Str::random(3)),
                'session_id'        => $sessionId,
                'status'            => 'pending',
                'api_key'           => 'ziezie_wa_' . Str::random(40),
                'user_id'           => $ownerId,
                'last_connected_at' => null,
                'qr_code'           => null,
                'is_trial'          => true,
                'quota'             => 0,
                'messages_sent'     => 0,
            ]);

            $this->createTrialForDevice($device, $ownerId);
        });

        return redirect()->route('device.index')->with('success', 'Device & trial berhasil dibuat.');
    }

    /**
     * Ambil QR sekali (snapshot) dan simpan di kolom qr_code
     */
    public function showQr($id, WaService $wa)
    {
        $device = \App\Models\Device::findOrFail($id);
        $this->authorizeDevice($device);

        $response = $wa->getQr($device->session_id);

        if ($response->successful()) {
            $qr = data_get($response->json(), 'qr');
            $device->update(['qr_code' => $qr]);
        }

        return view('device.qr', compact('device'));
    }

    public function liveQr($id, WaService $wa)
    {
        $device = \App\Models\Device::findOrFail($id);
        $this->authorizeDevice($device);

        $resp = $wa->getQr($device->session_id);

        if (!$resp->successful()) {
            Log::warning('qr_live_fail', ['status' => $resp->status(), 'body' => $resp->body()]);
            return response()->json(['qr' => null, 'connected' => false], 500);
        }

        $json = $resp->json() ?? [];
        $qr   = data_get($json, 'qr');           // "data:image/png;base64,..."
        $msg  = (string) data_get($json, 'msg'); // "already connected" kalau sudah konek
        $ok   = (bool) data_get($json, 'ok');

        $connected = $msg === 'already connected';

        return response()->json([
            'ok'        => $ok,
            'qr'        => $qr,
            'connected' => $connected,
        ]);
    }


    public function checkStatus($id, WaService $wa)
    {
        $device = \App\Models\Device::findOrFail($id);
        $this->authorizeDevice($device);

        Log::info('Hit /info for session: ' . $device->session_id);

        $res = $wa->getStatus($device->session_id);
        if (!$res->successful()) {
            Log::warning('WA /info FAILED', [
                'code' => $res->status(),
                'body' => $res->body(),
            ]);
            return back()->with('error', 'Gagal memeriksa status: ' . $res->status());
        }

        $info  = $res->json() ?: [];
        $ok    = (bool) data_get($info, 'ok', true); // Go balikin ok:true
        $ready = (bool) data_get($info, 'ready', false);
        $me    = (string) data_get($info, 'me', '');

        if (!$ok) {
            return back()->with('error', 'Server WA mengembalikan ok:false');
        }

        $normalized = $ready ? 'connected' : 'pending';

        $device->update([
            'status'            => $normalized,
            'last_connected_at' => $ready ? now() : null,
        ]);

        return back()->with('success', "Status diperbarui: {$normalized}" . ($me ? " (me={$me})" : ""));
    }



    private function normalizeWaStatus(?string $raw): ?string
    {
        if (!$raw) return null;
        $s = strtolower($raw);

        // nilai yang dianggap CONNECTED
        if (in_array($s, ['connected', 'open', 'authenticated', 'authorized', 'ready', 'online'])) {
            return 'connected';
        }

        // nilai yang dianggap PENDING (QR/connecting)
        if (in_array($s, ['pending', 'qr', 'scan_qr', 'connecting', 'pairing', 'init'])) {
            return 'pending';
        }

        // selain itu anggap DISCONNECTED
        if (in_array($s, ['disconnected', 'close', 'closed', 'offline', 'not_found', 'failed'])) {
            return 'disconnected';
        }

        // fallback: kalau mengandung kata kunci
        if (str_contains($s, 'auth') || str_contains($s, 'open')) return 'connected';
        if (str_contains($s, 'qr') || str_contains($s, 'connect')) return 'pending';
        if (str_contains($s, 'close') || str_contains($s, 'offline')) return 'disconnected';

        return null;
    }

    public function destroy($id, WaService $wa)
    {
        $device = \App\Models\Device::findOrFail($id);
        $this->authorizeDevice($device);

        $wa->deleteDevice($device->session_id); // abaikan response; tetap hapus lokal

        $device->delete();
        return back()->with('success', 'Device dihapus.');
    }

    public function log()
    {
        // halaman log kamu (tidak diubah)
        $q = Device::query()->orderByDesc('id');
        if (auth()->user()->role !== 'admin') {
            $q->where('user_id', auth()->id());
        }
        $devices = $q->get();

        return view('log.index', compact('devices'));
    }

    public function generateApiKey($id)
    {
        $device = Device::findOrFail($id);
        $this->authorizeDevice($device);

        if (!$device->api_key) {
            $device->api_key = 'ziezie_wa_' . Str::random(32);
            $device->save();
        }

        return response()->json(['api_key' => $device->api_key]);
    }

    /**
     * Kirim TEXT dari aplikasi eksternal (API) + log ke messages
     */
    public function sendFromExternal(Request $request, WaService $wa)
    {
        $request->validate([
            'api_key' => 'required|string',
            'to'      => 'required|string',
            'message' => 'required|string',
        ]);

        $device = Device::where('api_key', $request->api_key)->first();
        if (!$device) {
            return response()->json(['status' => false, 'message' => 'Unauthorized: Invalid API key'], 401);
        }

        $sessionId = $device->session_id;

        // Normalisasi JID (private → @s.whatsapp.net, group biarkan @g.us bila sudah benar)
        $to = trim($request->to);
        $recipient = str_ends_with($to, '@g.us')
            ? $to
            : preg_replace('/@.*/', '', $to) . '@s.whatsapp.net';

        // ====== KUOTA: reserve ======
        $reserve = $this->reserveQuota($device, 'text', 1);
        if (!$reserve['ok']) {
            return response()->json([
                'status'  => false,
                'error'   => 'quota',
                'message' => $reserve['reason'] ?? 'Kuota tidak tersedia',
            ], 402);
        }
        $sub = $reserve['sub']; // untuk commit/rollback nanti

        $msg = Message::create([
            'user_id'        => $device->user_id,
            'device_id'      => $device->id,
            'sender'         => null,
            'recipient'      => $recipient,
            'recipient_type' => str_ends_with($recipient, '@g.us') ? 'group' : 'user',
            'message_type'   => 'text',
            'content'        => $request->message,
            'status'         => 'queued',
            'provider'       => 'api',
        ]);

        try {
            $res = $wa->sendMessage([
                'to'      => $recipient,
                'text'    => $request->message,
                'session' => $sessionId,
                'isGroup' => str_ends_with($recipient, '@g.us'),
            ]);

            if ($res->successful()) {
                $msg->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                    'meta'    => $res->json(),
                ]);

                // ====== KUOTA: commit ======
                $this->commitUsage($device, $sub, 1);

                return response()->json([
                    'status'      => true,
                    'message'     => 'Pesan berhasil dikirim ke WA',
                    'wa_response' => $res->json(),
                ]);
            } else {
                $msg->update(['status' => 'failed', 'error_message' => $res->body()]);
                // ====== KUOTA: rollback ======
                $this->rollbackQuota($sub, 1);

                return response()->json(['status' => false, 'message' => 'Gagal mengirim ke server WA', 'error' => $res->body()], 500);
            }
        } catch (\Exception $e) {
            $msg->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            // ====== KUOTA: rollback ======
            $this->rollbackQuota($sub, 1);

            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    public function sendMediaFromExternal(Request $request, WaService $wa)
    {
        $request->validate([
            'api_key'  => 'required|string',
            'to'       => 'required|string',
            'file'     => 'required|file|max:4096',
            'caption'  => 'nullable|string'
        ]);

        $device = Device::where('api_key', $request->api_key)->first();
        if (!$device) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $sessionId = $device->session_id;

        $to = trim($request->to);
        $recipient = str_ends_with($to, '@g.us')
            ? $to
            : preg_replace('/@.*/', '', $to) . '@s.whatsapp.net';

        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension());
        $type = ($ext === 'pdf') ? 'pdf' : 'media';  // gating via plan

        // ====== KUOTA: reserve (mis. 1 unit per media; kalau mau 2, ganti ke 2)
        $units   = 1;
        $reserve = $this->reserveQuota($device, $type, $units);
        if (!$reserve['ok']) {
            return response()->json([
                'status'  => false,
                'error'   => 'quota',
                'message' => $reserve['reason'] ?? 'Kuota tidak tersedia'
            ], 402);
        }
        $sub = $reserve['sub'];

        $filename = Str::random(16) . '.' . $ext;
        $path = $file->storeAs('temp-wa', $filename);

        $msg = Message::create([
            'user_id'        => $device->user_id,
            'device_id'      => $device->id,
            'recipient'      => $recipient,
            'recipient_type' => str_ends_with($recipient, '@g.us') ? 'group' : 'user',
            'message_type'   => 'document', // atau deteksi mimetype
            'content'        => $request->caption ?? '',
            'status'         => 'queued',
            'provider'       => 'api',
            'meta'           => ['filename' => $filename],
        ]);

        try {
            $response = $wa->sendMediaBuffer(
                $sessionId,
                storage_path('app/' . $path),
                $filename,
                [
                    'to'       => $recipient,
                    'session'  => $sessionId,
                    'caption'  => $request->caption ?? '',
                    'filename' => $filename,
                ]
            );

            Storage::delete($path);

            if ($response->successful()) {
                $msg->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                    'meta'    => array_merge($msg->meta ?? [], ['wa_response' => $response->json()]),
                ]);

                // ====== KUOTA: commit ======
                $this->commitUsage($device, $sub, $units);

                return response()->json(['status' => true, 'message' => 'Media berhasil dikirim']);
            } else {
                $msg->update(['status' => 'failed', 'error_message' => $response->body()]);
                // ====== KUOTA: rollback ======
                $this->rollbackQuota($sub, $units);

                return response()->json(['status' => false, 'message' => 'Gagal kirim media', 'error' => $response->body()], 500);
            }
        } catch (\Exception $e) {
            Storage::delete($path);
            $msg->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            // ====== KUOTA: rollback ======
            $this->rollbackQuota($sub, $units);

            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    /* =======================
     * Helpers
     * ======================= */

    /**
     * Buat subscription trial per-device (device-based)
     * - Cari Plan "Trial" (atau price=0). Kalau tidak ada, pakai default 7 hari / 300 quota.
     */
    protected function createTrialSubscriptionForDevice(Device $device): void
    {
        // cari plan trial
        $trialPlan = Plan::where('name', 'Trial')->first()
            ?? Plan::where('price', 0)->first();

        if ($trialPlan) {
            $duration = (int) ($trialPlan->duration ?? 7);      // hari
            $quota    = (int) ($trialPlan->quota_limit ?? 300); // quota
            $planId   = $trialPlan->id;
        } else {
            // fallback sederhana
            $duration = 7;
            $quota    = 300;
            $planId   = null;
        }

        Subscription::create([
            'device_id'       => $device->id,
            'plan_id'         => $planId,
            'started_at'      => now(),
            'expired_at'      => now()->addDays($duration),
            'remaining_quota' => $quota,
            'is_trial'        => true,
            'user_id'      => $device->user_id, // kalau masih dipakai di model lama
        ]);
    }

    /**
     * Kurangi 1 kuota subscription aktif device jika ada (tidak boleh negatif)
     */
    protected function decrementDeviceQuota(Device $device): void
    {
        $sub = Subscription::where('device_id', $device->id)
            ->whereDate('expired_at', '>=', now())
            ->latest('id')
            ->first();

        if ($sub && $sub->remaining_quota !== null && $sub->remaining_quota > 0) {
            $sub->decrement('remaining_quota', 1);
        }
    }

    /**
     * Pastikan user biasa hanya akses device miliknya.
     */
    protected function authorizeDevice(Device $device): void
    {
        if (auth()->user()->role !== 'admin' && $device->user_id !== auth()->id()) {
            abort(403, 'Forbidden');
        }
    }

    // di controller tempat create device
    private function createTrialForDevice(\App\Models\Device $device, int $ownerId): void
    {
        $trialDays  = 7;     // durasi trial (ubah sesuai kebutuhan)
        $trialQuota = 500;   // kuota trial (ubah sesuai kebutuhan)

        \App\Models\Subscription::create([
            'user_id'         => $ownerId,
            'device_id'       => $device->id,       // <— wajib isi
            'plan_id'         => 1,              // atau isi ID plan trial jika ada di tabel plans
            'is_trial'        => true,
            'started_at'      => now(),
            'expired_at'      => now()->addDays($trialDays),
            'remaining_quota' => $trialQuota,
        ]);

        // sinkron status device opsional
        $device->update([
            'is_trial'      => true,
            'quota'         => $trialQuota,
            'messages_sent' => 0,
        ]);
    }
    public function webhook(Request $request)
    {
        // (opsional) verifikasi HMAC
        Log::info('WA WEBHOOK RAW', ['payload' => $request->all()]);
        return response()->json(['ok' => true]);
    }


    //helpoer
    private function getActiveSubWithPlan(Device $device)
    {
        // Ambil subscription aktif + plan-nya, LOCK untuk safety
        return $device->activeSubscription()
            ->with('plan:id,quota_limit,can_image,can_pdf,can_autoreply')
            ->lockForUpdate()
            ->first();
    }

    /**
     * Reserve kuota: - pastikan subscription aktif, - cek fitur paket, - kurangi remaining_quota.
     * @return array{ok:bool, reason:?string, sub:?Subscription}
     */
    private function reserveQuota(Device $device, string $usageType = 'text', int $units = 1): array
    {
        return DB::transaction(function () use ($device, $usageType, $units) {
            $sub = $this->getActiveSubWithPlan($device);
            if (!$sub) {
                return ['ok' => false, 'reason' => 'Tidak ada subscription aktif', 'sub' => null];
            }
            if ($sub->expired_at && $sub->expired_at->isPast()) {
                return ['ok' => false, 'reason' => 'Subscription sudah kedaluwarsa', 'sub' => $sub];
            }

            $plan = $sub->plan; // bisa null kalau relasinya belum di-load
            // Feature gating sederhana
            if ($usageType === 'media' && $plan && !$plan->can_image) {
                return ['ok' => false, 'reason' => 'Paket Anda tidak mengizinkan kirim media', 'sub' => $sub];
            }
            if ($usageType === 'pdf' && $plan && !$plan->can_pdf) {
                return ['ok' => false, 'reason' => 'Paket Anda tidak mengizinkan kirim PDF', 'sub' => $sub];
            }

            // Hitung kuota: pakai subscriptions.remaining_quota jika tidak null
            // (Kalau null → artinya unlimited)
            if (!is_null($sub->remaining_quota)) {
                if ($sub->remaining_quota < $units) {
                    return ['ok' => false, 'reason' => 'Kuota habis', 'sub' => $sub];
                }
                $sub->remaining_quota = $sub->remaining_quota - $units;
                $sub->save();
            }

            // Opsi: sinkron cache ke device (jika kamu pakai field ini untuk tampilan)
            $device->decrement('quota', 0); // no-op; cuma contoh kalau mau ikut kurangi di sini juga

            return ['ok' => true, 'reason' => null, 'sub' => $sub];
        });
    }

    /** Commit hitungan “terkirim” (mis. statistik). Kuota sudah dikurangi saat reserve. */
    private function commitUsage(Device $device, Subscription $sub, int $units = 1): void
    {
        // Kalau kamu punya kolom messages_sent di subscriptions/devices, update di sini.
        // Contoh:
        // $sub->increment('messages_sent', $units);
        // $device->increment('messages_sent', $units);
    }

    /** Rollback kuota jika pengiriman gagal. */
    private function rollbackQuota(Subscription $sub, int $units = 1): void
    {
        if (!is_null($sub->remaining_quota)) {
            $sub->increment('remaining_quota', $units);
        }
    }
}
