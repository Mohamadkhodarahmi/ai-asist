<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Plan;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\SubscriptionChangedNotification;
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

            // Store old plan for comparison
            $oldPlan = $order->user->plan?->name ?? 'Free';

            // Assign plan to user
            if ($order->plan_id) {
                $plan = Plan::query()->find($order->plan_id);
                if ($plan) {
                    $order->user->plan()->associate($plan);
                    $order->user->save();

                    // Send payment received notification
                    $order->user->notify(new PaymentReceivedNotification($order));

                    // Send subscription changed notification
                    $type = $this->determinePlanChangeType($oldPlan, $plan->name);
                    $order->user->notify(new SubscriptionChangedNotification(
                        oldPlan: $oldPlan,
                        newPlan: $plan->name,
                        type: $type,
                        features: $this->getPlanFeatures($plan)
                    ));
                }
            }
        } elseif (in_array($paymentStatus, ['failed', 'expired', 'refunded', 'chargeback'], true)) {
            $order->update(['status' => 'failed']);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Determine if the plan change is an upgrade or downgrade.
     */
    private function determinePlanChangeType(string $oldPlan, string $newPlan): string
    {
        $planHierarchy = ['Free' => 0, 'Starter' => 1, 'Pro' => 2, 'Business' => 3];

        $oldLevel = $planHierarchy[$oldPlan] ?? 0;
        $newLevel = $planHierarchy[$newPlan] ?? 0;

        if ($newLevel > $oldLevel) {
            return 'upgrade';
        } elseif ($newLevel < $oldLevel) {
            return 'downgrade';
        }

        return 'changed';
    }

    /**
     * Get features for a plan.
     *
     * @return array<int, string>
     */
    private function getPlanFeatures(Plan $plan): array
    {
        return match ($plan->slug) {
            'starter' => [
                'Unlimited conversations',
                'Export data in multiple formats',
                'Document analytics',
                'API access with higher limits',
            ],
            'pro' => [
                'Everything in Starter',
                'Advanced analytics dashboard',
                'Priority support',
                'Team collaboration features',
                'Custom AI personalities',
            ],
            'business' => [
                'Everything in Pro',
                'Enterprise-grade security',
                'Dedicated account manager',
                'Custom integrations',
                'SLA guarantee',
            ],
            default => [],
        };
    }
}
