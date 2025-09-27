<?php

namespace App\Http\Controllers;

use App\Models\AutoReply;
use App\Models\Broadcast;
use App\Models\Device;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUser = User::count();
        $userTrial = Subscription::whereNull('plan_id')->count(); // asumsi trial = tanpa plan
        $totalIncome = Payment::sum('amount');
        $expiringSoon = Subscription::whereDate('expired_at', '<=', now()->addDays(3))->count();

        // Untuk Chart
        $plans = Plan::withCount('subscriptions')->get();
        $planLabels = $plans->pluck('name');
        $planCounts = $plans->pluck('subscriptions_count');

        return view('admin.dashboard', compact(
            'totalUser',
            'userTrial',
            'totalIncome',
            'expiringSoon',
            'planLabels',
            'planCounts'
        ));
    }
    public function users()
    {
        $users = User::with(['subscription.plan'])->latest()->get();

        return view('admin.users.index', compact('users'));
    }
    public function deviceList()
    {
        $devices = Device::with('user')->latest()->get();

        return view('admin.devices.index', compact('devices'));
    }
    //PLAN
    public function indexplan()
    {
        $plans = Plan::latest()->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function storeplan(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'unique:plans,name'],
            'price'        => ['required', 'numeric', 'min:0'],
            'duration'     => ['required', 'integer', 'min:1'],        // hari
            'max_devices'  => ['required', 'integer', 'min:1'],
            'quota_limit'  => ['required', 'integer', 'min:0'],
            'can_image'    => ['nullable', 'boolean'],
            'can_pdf'      => ['nullable', 'boolean'],
            'can_autoreply' => ['nullable', 'boolean'],
        ]);

        $data['can_image']     = (bool) $request->boolean('can_image');
        $data['can_pdf']       = (bool) $request->boolean('can_pdf');
        $data['can_autoreply'] = (bool) $request->boolean('can_autoreply');

        Plan::create($data);

        return back()->with('success', 'Plan berhasil ditambahkan.');
    }

    public function fetchplan(Plan $plan)
    {
        return response()->json($plan);
    }

    public function updateplan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'unique:plans,name,' . $plan->id],
            'price'        => ['required', 'numeric', 'min:0'],
            'duration'     => ['required', 'integer', 'min:1'],
            'max_devices'  => ['required', 'integer', 'min:1'],
            'quota_limit'  => ['required', 'integer', 'min:0'],
            'can_image'    => ['nullable', 'boolean'],
            'can_pdf'      => ['nullable', 'boolean'],
            'can_autoreply' => ['nullable', 'boolean'],
        ]);

        $data['can_image']     = (bool) $request->boolean('can_image');
        $data['can_pdf']       = (bool) $request->boolean('can_pdf');
        $data['can_autoreply'] = (bool) $request->boolean('can_autoreply');

        $plan->update($data);

        return back()->with('success', 'Plan berhasil diperbarui.');
    }

    public function destroyplan(Plan $plan)
    {
        $plan->delete();
        return back()->with('success', 'Plan berhasil dihapus.');
    }
    public function indexpayment(Request $request)
    {
        $status = $request->get('status', 'all'); // paid|pending|failed|expired|refunded|all
        $from   = $request->get('from');
        $to     = $request->get('to');

        $q = Payment::with(['user:id,name,email', 'plan:id,name'])->latest();

        if ($status && $status !== 'all') {
            $q->where('status', $status);
        }
        if ($from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $q->whereDate('created_at', '<=', $to);
        }

        $payments = $q->paginate(20);

        // summary cepat (pakai query baru agar tidak ikut paginate)
        $base = Payment::query();
        if ($status && $status !== 'all') $base->where('status', $status);
        if ($from) $base->whereDate('created_at', '>=', $from);
        if ($to)   $base->whereDate('created_at', '<=', $to);

        $summary = [
            'paid'    => (clone $base)->where('status', 'paid')->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'failed'  => (clone $base)->where('status', 'failed')->count(),
            'expired' => (clone $base)->where('status', 'expired')->count(),
            'refunded' => (clone $base)->where('status', 'refunded')->count(),
            'total_paid_amount' => (float) (clone $base)->where('status', 'paid')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'summary', 'status', 'from', 'to'));
    }

    /**
     * Ringkasan angka untuk widget (AJAX optional)
     */
    public function summarypayment(Request $request)
    {
        $q = Payment::query();

        if ($status = $request->get('status')) {
            if ($status !== 'all') $q->where('status', $status);
        }
        if ($from = $request->get('from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $q->whereDate('created_at', '<=', $to);
        }

        $data = [
            'count_paid'   => (clone $q)->where('status', 'paid')->count(),
            'count_pending' => (clone $q)->where('status', 'pending')->count(),
            'count_failed' => (clone $q)->where('status', 'failed')->count(),
            'total_paid_amount' => (float) (clone $q)->where('status', 'paid')->sum('amount'),
            'count_today'  => (int) Payment::whereDate('created_at', now()->toDateString())->count(),
        ];

        return response()->json($data);
    }

    /**
     * Detail payment (JSON) untuk modal
     */
    public function fetchpayment(Payment $payment)
    {
        $payment->load(['user:id,name,email', 'plan:id,name']);
        return response()->json($payment);
    }

    /**
     * Update status payment (manual correction)
     */
    public function updatepayment(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'status' => 'required|in:paid,pending,failed,expired,refunded',
        ]);

        // auto set paid_at saat jadi paid
        if ($data['status'] === 'paid' && !$payment->paid_at) {
            $payment->paid_at = now();
        }
        // kalau bukan paid, jangan sentuh paid_at (biar histori aman)

        $payment->status = $data['status'];
        $payment->save();

        return back()->with('success', 'Status payment diperbarui.');
    }

    //handle pesan
    public function indexmessages(Request $r)
    {
        $status   = $r->get('status', 'all'); // queued|sent|failed|pending|all
        $from     = $r->get('from');
        $to       = $r->get('to');
        $deviceId = $r->get('device_id');

        $q = Message::with(['device:id,name,session_id', 'user:id,name,email'])->latest();

        if ($status !== 'all') $q->where('status', $status);
        if ($from) $q->whereDate('created_at', '>=', $from);
        if ($to)   $q->whereDate('created_at', '<=', $to);
        if ($deviceId) $q->where('device_id', $deviceId);

        $messages = $q->paginate(20);

        // summary kotak
        $base = Message::query();
        if ($status !== 'all') $base->where('status', $status);
        if ($from) $base->whereDate('created_at', '>=', $from);
        if ($to)   $base->whereDate('created_at', '<=', $to);
        if ($deviceId) $base->where('device_id', $deviceId);

        $summary = [
            'sent'    => (clone $base)->where('status', 'sent')->count(),
            'failed'  => (clone $base)->where('status', 'failed')->count(),
            'queued'  => (clone $base)->where('status', 'queued')->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
        ];

        $devices = Device::orderBy('name')->get(['id', 'name']); // buat filter dropdown

        return view('admin.messages.index', compact('messages', 'summary', 'status', 'from', 'to', 'deviceId', 'devices'));
    }

    // Ringkasan angka (AJAX)
    public function summarymessages(Request $r)
    {
        $q = Message::query();
        if ($status = $r->get('status')) if ($status !== 'all') $q->where('status', $status);
        if ($from = $r->get('from')) $q->whereDate('created_at', '>=', $from);
        if ($to = $r->get('to')) $q->whereDate('created_at', '<=', $to);
        if ($deviceId = $r->get('device_id')) $q->where('device_id', $deviceId);

        return response()->json([
            'sent'    => (clone $q)->where('status', 'sent')->count(),
            'failed'  => (clone $q)->where('status', 'failed')->count(),
            'queued'  => (clone $q)->where('status', 'queued')->count(),
            'pending' => (clone $q)->where('status', 'pending')->count(),
        ]);
    }

    // Detail 1 pesan
    public function fetchmessage(Message $message)
    {
        $message->load(['device:id,name,session_id', 'user:id,name,email']);
        return response()->json($message);
    }

    // Hapus log
    public function destroymessage(Message $message)
    {
        $message->delete();
        return back()->with('success', 'Log pesan dihapus.');
    }

    // Kirim ulang (set status queued; proses pengiriman ditangani worker/gateway)
    public function retrysentmessage(Message $message)
    {
        // Hanya pesan gagal yang bisa diretry
        if (!in_array($message->status, ['failed', 'pending'])) {
            return back()->with('error', 'Hanya pesan gagal/pending yang bisa dikirim ulang.');
        }

        $message->update([
            'status' => 'queued',
            'error_message' => null,
            'retry_count' => $message->retry_count + 1,
            'sent_at' => null,
        ]);

        // TODO: Trigger ke WA gateway kamu (Node.js) di sini jika diperlukan,
        // misal lewat Job/Service. Untuk sekarang kita set queued dulu.

        return back()->with('success', 'Pesan dijadwalkan ulang (queued).');
    }
    //autoreply
    public function indexautoreply(Request $r)
    {
        $status   = $r->get('status', 'all');   // all|active|inactive
        $deviceId = $r->get('device_id');

        $q = AutoReply::with(['device:id,name', 'user:id,name'])->orderByDesc('id');
        if ($status === 'active')   $q->where('active', true);
        if ($status === 'inactive') $q->where('active', false);
        if ($deviceId)              $q->where('device_id', $deviceId);

        $rules   = $q->paginate(20);
        $devices = Device::orderBy('name')->get(['id', 'name']);

        // Ringkas
        $summary = [
            'total'    => Autoreply::count(),
            'active'   => Autoreply::where('active', true)->count(),
            'inactive' => Autoreply::where('active', false)->count(),
        ];

        return view('admin.autoreplies.index', compact('rules', 'devices', 'status', 'deviceId', 'summary'));
    }

    public function storeautoreply(Request $r)
    {
        $data = $r->validate([
            'keyword'   => ['required', 'string', 'max:100'],
            'response'  => ['required', 'string'],
            'type'      => ['required', 'in:text,image,pdf,document'],
            'device_id' => ['nullable', 'integer', 'exists:devices,id'],
            'user_id'   => ['nullable', 'integer', 'exists:users,id'],
            'active'    => ['nullable', 'boolean'],
        ]);

        $data['active'] = $r->boolean('active');
        // Jika admin membuat, kamu bisa auto‑set user_id admin:
        // $data['user_id'] = $data['user_id'] ?? auth()->id();

        Autoreply::create($data);
        return back()->with('success', 'Auto reply dibuat.');
    }

    public function fetchautoreply(Autoreply $ar)
    {
        $ar->load(['device:id,name', 'user:id,name']);
        return response()->json($ar);
    }

    public function updateautoreply(Request $r, Autoreply $ar)
    {
        $data = $r->validate([
            'keyword'   => ['required', 'string', 'max:100'],
            'response'  => ['required', 'string'],
            'type'      => ['required', 'in:text,image,pdf,document'],
            'device_id' => ['nullable', 'integer', 'exists:devices,id'],
            'user_id'   => ['nullable', 'integer', 'exists:users,id'],
            'active'    => ['nullable', 'boolean'],
        ]);

        $data['active'] = $r->boolean('active');
        $ar->update($data);

        return back()->with('success', 'Auto reply diperbarui.');
    }

    public function toggleautoreply(Autoreply $ar)
    {
        $ar->active = ! $ar->active;
        $ar->save();

        return back()->with('success', 'Status auto reply diubah.');
    }

    public function destroyautoreply(Autoreply $ar)
    {
        $ar->delete();
        return back()->with('success', 'Auto reply dihapus.');
    }
    //broadcast
    public function indexbroadcast(Request $r)
    {
        $status   = $r->get('status', 'all');    // all|active|inactive
        $deviceId = $r->get('device_id');
        $userId   = $r->get('user_id');
        $from     = $r->get('from');
        $to       = $r->get('to');

        $q = Broadcast::with(['device:id,name,user_id', 'user:id,name,email,role'])->latest();

        if ($status === 'active')   $q->where('active', true);
        if ($status === 'inactive') $q->where('active', false);
        if ($deviceId)              $q->where('device_id', $deviceId);
        if ($userId)                $q->where('user_id', $userId);
        if ($from)                  $q->whereDate('created_at', '>=', $from);
        if ($to)                    $q->whereDate('created_at', '<=', $to);

        $broadcasts = $q->paginate(20);

        // ringkasan
        $base = Broadcast::query();
        if ($status === 'active')   $base->where('active', true);
        if ($status === 'inactive') $base->where('active', false);
        if ($deviceId)              $base->where('device_id', $deviceId);
        if ($userId)                $base->where('user_id', $userId);
        if ($from)                  $base->whereDate('created_at', '>=', $from);
        if ($to)                    $base->whereDate('created_at', '<=', $to);

        $summary = [
            'total'   => (clone $base)->count(),
            'active'  => (clone $base)->where('active', true)->count(),
            'inactive' => (clone $base)->where('active', false)->count(),
        ];

        // dropdown data
        $devices = Device::orderBy('name')->get(['id', 'name', 'user_id']);
        $users   = User::orderBy('name')->get(['id', 'name', 'email', 'role']);

        // daftar device yg pemiliknya role admin (untuk form create/edit)
        $adminDevices = Device::whereHas('user', function ($w) {
            $w->where('role', 'admin');
        })->orderBy('name')->get(['id', 'name', 'user_id']);

        return view('admin.broadcasts.index', compact(
            'broadcasts',
            'summary',
            'status',
            'deviceId',
            'userId',
            'from',
            'to',
            'devices',
            'users',
            'adminDevices'
        ));
    }

    /**
     * STORE: hanya boleh untuk device yang pemiliknya role admin
     */
    public function storebroadcast(Request $r)
    {
        $data = $r->validate([
            'message'    => ['required', 'string'],
            'send_time'  => ['required', 'date_format:H:i'],
            'groups'     => ['nullable', 'array'],
            'groups.*'   => ['string'],
            'device_id'  => ['required', 'integer', 'exists:devices,id'],
            'user_id'    => ['required', 'integer', 'exists:users,id'],
            'active'     => ['nullable', 'boolean'],
        ]);

        // Validasi: device harus milik user role admin
        $device = Device::with('user:id,role')->findOrFail($data['device_id']);
        $user   = User::findOrFail($data['user_id']);

        if (($device->user->role ?? null) !== 'admin' || $user->role !== 'admin') {
            return back()->with('error', 'Hanya boleh membuat broadcast untuk device & user ber‑role admin.');
        }

        $data['active'] = $r->boolean('active');
        Broadcast::create($data);

        return back()->with('success', 'Broadcast dibuat.');
    }

    /**
     * FETCH detail 1 broadcast (JSON)
     */
    public function fetchbroadcast(Broadcast $broadcast)
    {
        $broadcast->load(['device:id,name,user_id', 'user:id,name,email,role']);
        return response()->json($broadcast);
    }

    /**
     * UPDATE: hanya boleh kalau broadcast milik user role admin
     */
    public function updatebroadcast(Request $r, Broadcast $broadcast)
    {
        $data = $r->validate([
            'message'    => ['required', 'string'],
            'send_time'  => ['required', 'date_format:H:i'],
            'groups'     => ['nullable', 'array'],
            'groups.*'   => ['string'],
            'device_id'  => ['required', 'integer', 'exists:devices,id'],
            'user_id'    => ['required', 'integer', 'exists:users,id'],
            'active'     => ['nullable', 'boolean'],
        ]);

        $broadcast->load('user:id,role', 'device.user:id,role');

        // izinkan edit hanya jika milik user role admin
        if (($broadcast->user->role ?? null) !== 'admin') {
            return back()->with('error', 'Tidak boleh mengedit broadcast milik user non‑admin.');
        }

        // kalau device/user diganti, tetap wajib role admin
        $device = Device::with('user:id,role')->findOrFail($data['device_id']);
        $user   = User::findOrFail($data['user_id']);
        if (($device->user->role ?? null) !== 'admin' || $user->role !== 'admin') {
            return back()->with('error', 'Device & user tujuan update harus ber‑role admin.');
        }

        $data['active'] = $r->boolean('active');
        $broadcast->update($data);

        return back()->with('success', 'Broadcast diperbarui.');
    }

    /**
     * TOGGLE: admin boleh menonaktifkan broadcast siapapun,
     * tapi TIDAK boleh mengaktifkan broadcast milik user non‑admin.
     */
    public function togglebroadcast(Broadcast $broadcast)
    {
        $broadcast->load('user:id,role');

        // Jika target adalah user non-admin dan saat ini inactive → tidak boleh diaktifkan
        if (($broadcast->user->role ?? null) !== 'admin' && $broadcast->active === false) {
            return back()->with('error', 'Tidak boleh mengaktifkan broadcast milik user non‑admin.');
        }

        $broadcast->active = ! $broadcast->active;
        $broadcast->save();

        return back()->with('success', $broadcast->active ? 'Broadcast diaktifkan.' : 'Broadcast dinonaktifkan.');
    }

    /**
     * DESTROY: hanya boleh kalau milik user role admin
     */
    public function destroybroadcast(Broadcast $broadcast)
    {
        $broadcast->load('user:id,role');

        if (($broadcast->user->role ?? null) !== 'admin') {
            return back()->with('error', 'Tidak boleh menghapus broadcast milik user non‑admin.');
        }

        $broadcast->delete();

        return back()->with('success', 'Broadcast dihapus.');
    }
    public function apiBroadcastsForGo()
    {
        $now = \Carbon\Carbon::now()->format('H:i');

        $rows = \App\Models\Broadcast::with('device:id,name,gateway_id')
            ->where('active', true)
            ->where('send_time', $now)
            ->get(['id', 'message', 'groups', 'device_id', 'send_time']);

        $out = $rows->map(function ($b) {
            return [
                'id'        => $b->id,
                'message'   => (string) $b->message,
                'groups'    => $b->groups ?? [],
                'device'    => $b->device->gateway_id ?? null, // ⬅️ kirim ID sesi wa-go
                'send_time' => $b->send_time,
            ];
        });

        return response()->json($out->values());
    }
}
