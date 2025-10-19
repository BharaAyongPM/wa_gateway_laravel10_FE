<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpgradeController extends Controller
{
    public function show(Request $request, Device $device)
    {
        // Hanya pemilik device / admin
        if ($request->user()->role !== 'admin' && $device->user_id !== $request->user()->id) {
            return back()->with('error', 'Unauthorized');
        }

        $plans = Plan::orderBy('price')->get();
        $sub   = $device->activeSubscription; // asumsi accessor

        return view('device.upgrade', compact('device', 'plans', 'sub'));
    }

    public function checkout(Request $request, Device $device)
    {
        Log::info('Checkout HIT', [
            'route'     => 'device.upgrade.checkout',
            'user_id'   => $request->user()->id ?? null,
            'device_id' => $device->id ?? null,
            'payload'   => $request->all(),
            'method'    => $request->method(),
            'url'       => $request->fullUrl(),
        ]);

        // Authz sederhana (kalau belum pakai policy)
        if ($request->user()->role !== 'admin' && $device->user_id !== $request->user()->id) {
            Log::warning('Checkout UNAUTHORIZED', ['user_id' => $request->user()->id, 'device_user' => $device->user_id]);
            return back()->with('error', 'Unauthorized');
        }

        try {
            $data = $request->validate([
                'plan_id' => 'required|exists:plans,id',
                // JANGAN validasi agree di sini
            ]);
        } catch (ValidationException $e) {
            Log::error('Checkout VALIDATION FAILED', [
                'errors'  => $e->errors(),
                'input'   => $request->all(),
            ]);
            throw $e; // biar balik dengan old() + $errors ke view
        }

        $plan = Plan::findOrFail($data['plan_id']);

        Log::info('Checkout PLAN OK', [
            'plan_id'   => $plan->id,
            'plan_name' => $plan->name,
            'price'     => $plan->price,
        ]);

        // Buat Payment (status pending)
        $orderNo = 'ORD-' . Str::upper(Str::random(10));
        $payment = Payment::create([
            'order_no'  => $orderNo,
            'user_id'   => $request->user()->id,
            'device_id' => $device->id,
            'plan_id'   => $plan->id,
            'status'    => 'pending',
            'amount'    => $plan->price,
        ]);

        Log::info('Checkout PAYMENT CREATED', [
            'payment_id' => $payment->id,
            'order_no'   => $payment->order_no,
            'amount'     => $payment->amount,
        ]);

        // Snap token
        try {
            $params = [
                'transaction_details' => [
                    'order_id'     => $payment->order_no,
                    'gross_amount' => (int) $plan->price,
                ],
                'item_details' => [[
                    'id'       => 'PLAN-' . $plan->id,
                    'price'    => (int) $plan->price,
                    'quantity' => 1,
                    'name'     => $plan->name,
                ]],
                'customer_details' => [
                    'first_name' => $request->user()->name ?? 'User',
                    'email'      => $request->user()->email ?? 'user@example.com',
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $payment->update(['snap_token' => $snapToken]);

            Log::info('Checkout SNAP TOKEN OK', ['order_no' => $payment->order_no, 'snap' => $snapToken]);
        } catch (\Throwable $e) {
            Log::error('Checkout SNAP TOKEN FAILED', [
                'order_no' => $payment->order_no,
                'error'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal membuat token pembayaran. Coba lagi.');
        }

        return view('payments.midtrans_checkout', [
            'device'     => $device,
            'plan'       => $plan,
            'payment'    => $payment,
            'snapToken'  => $snapToken,
            'clientKey'  => config('midtrans.client_key'),
        ]);
    }
}
