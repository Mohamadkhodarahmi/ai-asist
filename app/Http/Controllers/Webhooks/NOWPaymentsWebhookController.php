<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NOWPaymentsWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $ipnSecret = (string) config('nowpayments.ipn_secret');
        $receivedHmac = $request->header('x-nowpayments-sig');
        $payload = $request->getContent();

        // Verify signature
        if (! $ipnSecret || ! $receivedHmac) {
            return response()->json(['message' => 'Missing signature'], 400);
        }

        $calculated = hash_hmac('sha512', $payload, $ipnSecret);
        if (! hash_equals($calculated, $receivedHmac)) {
            Log::warning('NOWPayments IPN invalid signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $data = $request->json()->all();
        $orderId = (int) ($data['order_id'] ?? 0);
        $paymentStatus = (string) ($data['payment_status'] ?? '');

        $order = Order::query()->find($orderId);
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Only act on confirmed/finished statuses
        if (in_array($paymentStatus, ['finished', 'confirmed'], true)) {
            $order->update(['status' => 'paid']);

            // Assign plan to user
            if ($order->plan_id) {
                $plan = Plan::query()->find($order->plan_id);
                if ($plan) {
                    $order->user->plan()->associate($plan);
                    $order->user->save();
                }
            }
        } elseif (in_array($paymentStatus, ['failed', 'expired', 'refunded', 'chargeback'], true)) {
            $order->update(['status' => 'failed']);
        }

        return response()->json(['status' => 'ok']);
    }
}


