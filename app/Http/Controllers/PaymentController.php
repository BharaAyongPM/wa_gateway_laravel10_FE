<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Payment;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function notify(Request $request)
    {
        $payload = $request->all();

        $orderId   = $payload['order_id'] ?? null;
        $trxId     = $payload['transaction_id'] ?? null;
        $status    = $payload['transaction_status'] ?? null;
        $payType   = $payload['payment_type'] ?? null;
        $grossAmt  = $payload['gross_amount'] ?? null;
        $settleTime = $payload['settlement_time'] ?? null;

        if (!$orderId) return response()->json(['message' => 'order_id missing'], 400);

        $payment = Payment::where('order_no', $orderId)->first();
        if (!$payment) return response()->json(['message' => 'payment not found'], 404);

        $payment->update([
            'transaction_id'   => $trxId,
            'status'           => $status,          // pending / settlement / capture / expire / cancel
            'payment_type'     => $payType,
            'paid_at'          => $settleTime ? Carbon::parse($settleTime) : null,
            'midtrans_payload' => $payload,
        ]);

        // Jika sukses → aktifkan/perpanjang subscription
        if (in_array($status, ['capture', 'settlement'])) {
            $plan = $payment->plan;
            $device = $payment->device_id ? Device::find($payment->device_id) : null;

            if ($plan && $device) {
                $now = Carbon::now();
                $current = $device->activeSubscription; // accessor milikmu

                $start = $now;
                $end   = $now->copy()->addDays((int) $plan->duration);

                if ($current && $current->expired_at && $current->expired_at->gt($now)) {
                    $start = $current->expired_at->copy();
                    $end   = $current->expired_at->copy()->addDays((int) $plan->duration);
                }

                Subscription::create([
                    'user_id'         => $payment->user_id,
                    'device_id'       => $device->id,
                    'plan_id'         => $plan->id,
                    'started_at'      => $start,
                    'expired_at'      => $end,
                    'remaining_quota' => (int) $plan->quota_limit,
                    'is_trial'        => false,
                ]);
            }
        }

        return response()->json(['message' => 'ok']);
    }

    public function finish(Request $request)
    {
        $status   = $request->query('status'); // success|pending|error (dari snap callback)
        $orderNo  = $request->query('order');  // ORD-XXXX

        $payment = $orderNo ? Payment::where('order_no', $orderNo)->with(['plan'])->first() : null;

        return view('payments.finish', compact('status', 'orderNo', 'payment'));
    }
}
