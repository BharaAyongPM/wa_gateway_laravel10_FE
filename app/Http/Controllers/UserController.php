<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Message;
use App\Models\Subscription;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Ambil device + subscription aktif (eager load)
        $devices = Device::query()
            ->with(['activeSubscription.plan'])
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        $deviceIds = $devices->pluck('id');

        // Total pesan terkirim
        $totalSent = Message::whereIn('device_id', $deviceIds)
            ->where('status', 'sent')
            ->count();

        // Total kuota berjalan = sum remaining_quota dari subscription AKTIF per device
        $totalQuotaRemaining = Subscription::whereIn('device_id', $deviceIds)
            ->where('expired_at', '>=', now()) // pakai waktu penuh, bukan whereDate
            ->sum('remaining_quota');

        // Paket hampir habis: kuota <= threshold ATAU expired <= 3 hari lagi
        $THRESHOLD = 50;
        $almostOut = Subscription::whereIn('device_id', $deviceIds)
            ->where(function ($q) use ($THRESHOLD) {
                $q->where('remaining_quota', '<=', $THRESHOLD)
                    ->orWhere('expired_at', '<=', now()->addDays(3)); // jangan whereDate
            })
            ->count();

        // Ringkasan per device untuk tabel/box
        $deviceSummaries = $devices->map(function ($d) {
            $sub = $d->activeSubscription; // ← PENTING: gunakan relasi yang benar
            return [
                'id'               => $d->id,
                'name'             => $d->name,
                'plan'             => optional(optional($sub)->plan)->name ?? ($sub && $sub->is_trial ? 'Trial' : '-'),
                'remaining_quota'  => optional($sub)->remaining_quota,
                'expired_at'       => optional($sub)->expired_at,
                'status'           => $d->status,
                'is_trial'         => (bool) optional($sub)->is_trial,
            ];
        });

        return view('user.dashboard', compact(
            'devices',
            'deviceSummaries',
            'totalSent',
            'totalQuotaRemaining',
            'almostOut'
        ));
    }
    public function messagesHistory(Request $request)
    {
        $user = $request->user();

        // daftar device milik user (untuk dropdown filter)
        $devices = Device::where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        // ambil filter
        $status    = $request->query('status'); // sent|failed|queued (opsional)
        $deviceId  = $request->query('device_id'); // opsional
        $dateFrom  = $request->query('date_from'); // Y-m-d
        $dateTo    = $request->query('date_to');   // Y-m-d

        // query dasar
        $query = Message::with(['device:id,name'])
            ->where('user_id', $user->id);

        // filter status
        if ($status && in_array($status, ['sent', 'failed', 'queued'])) {
            $query->where('status', $status);
        }

        // filter device
        if ($deviceId) {
            // pastikan device memang milik user
            $query->where('device_id', function ($q) use ($deviceId, $user) {
                $q->from('devices')
                    ->select('id')
                    ->where('id', $deviceId)
                    ->where('user_id', $user->id)
                    ->limit(1);
            });
        }

        // filter tanggal: gunakan created_at sebagai acuan
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // sorting terbaru dulu, paginate
        $messages = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // statistik kecil untuk header (opsional)
        $stats = [
            'all'    => Message::where('user_id', $user->id)->count(),
            'sent'   => Message::where('user_id', $user->id)->where('status', 'sent')->count(),
            'failed' => Message::where('user_id', $user->id)->where('status', 'failed')->count(),
            'queued' => Message::where('user_id', $user->id)->where('status', 'queued')->count(),
        ];

        return view('user.messages-history', compact('devices', 'messages', 'stats', 'status', 'deviceId', 'dateFrom', 'dateTo'));
    }
}
