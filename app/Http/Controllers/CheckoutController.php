<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\NOWPaymentsClient;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $plan = Plan::query()->findOrFail($request->integer('plan_id'));

        if ($plan->price_cents === null || $plan->price_cents <= 0) {
            return redirect()->route('pricing');
        }

        return view('checkout', [
            'plan' => $plan,
        ]);
    }

    public function pay(Request $request, NOWPaymentsClient $client): RedirectResponse
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $user = Auth::user();
        $plan = Plan::query()->findOrFail($request->integer('plan_id'));

        // Create local order record first
        $order = Order::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price_cents,
            'status' => 'pending',
            'provider' => 'nowpayments',
        ]);

        // Ensure API key configured
        if (! config('nowpayments.api_key')) {
            return redirect()->route('pricing')->with('error', 'Payment provider is not configured.');
        }

        // Create NOWPayments invoice
        $callbackUrl = route('webhooks.nowpayments');
        $payload = [
            'price_amount' => number_format($plan->price_cents / 100, 2, '.', ''),
            'price_currency' => 'USD',
            'order_id' => (string) $order->id,
            'order_description' => 'Plan: '.$plan->name,
            'success_url' => route('chat'),
            'cancel_url' => route('pricing'),
            'ipn_callback_url' => $callbackUrl,
        ];

        try {
            $invoice = $client->createInvoice($payload);
        } catch (\Throwable $e) {
            // Mark order failed for visibility
            $order->update(['status' => 'failed']);

            return redirect()->route('pricing')->with('error', 'Payment could not be initialized: '.$e->getMessage());
        }

        $order->update([
            'provider_reference' => $invoice['id'] ?? null,
        ]);

        // Redirect to NOWPayments hosted invoice URL
        return redirect()->away($invoice['invoice_url'] ?? route('pricing'));
    }
}


