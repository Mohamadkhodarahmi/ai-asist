<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillingController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Show billing dashboard
     */
    public function index(): View
    {
        $user = Auth::user();

        $activeSubscription = $user->activeSubscription;
        if ($activeSubscription) {
            $activeSubscription->load('plan');
        }
        $subscriptions = $user->subscriptions()->with('plan')->latest()->get();
        $orders = $user->orders()->with('plan')->latest()->get();
        $plans = Plan::where('slug', '!=', 'free')->get();

        return view('billing.index', compact(
            'activeSubscription',
            'subscriptions',
            'orders',
            'plans'
        ));
    }

    /**
     * Show subscription details
     */
    public function subscription(Subscription $subscription): View
    {
        $this->authorize('view', $subscription);

        $orders = $subscription->orders()->latest()->get();

        return view('billing.subscription', compact('subscription', 'orders'));
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $request->validate([
            'immediate' => 'boolean',
        ]);

        $immediate = $request->boolean('immediate', false);
        $this->subscriptionService->cancelSubscription($subscription, $immediate);

        return redirect()->route('billing.index')
            ->with('success', $immediate ?
                'Subscription cancelled immediately.' :
                'Subscription will be cancelled at the end of the current billing period.');
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $this->subscriptionService->resumeSubscription($subscription);

        return redirect()->route('billing.index')
            ->with('success', 'Subscription resumed successfully.');
    }

    /**
     * Change subscription plan
     */
    public function changePlan(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'prorate' => 'boolean',
        ]);

        $newPlan = Plan::findOrFail($request->plan_id);
        $prorate = $request->boolean('prorate', true);

        $this->subscriptionService->changePlan($subscription, $newPlan, $prorate);

        return redirect()->route('billing.index')
            ->with('success', 'Plan changed successfully.');
    }

    /**
     * Extend trial
     */
    public function extendTrial(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $request->validate([
            'days' => 'required|integer|min:1|max:30',
        ]);

        $this->subscriptionService->extendTrial($subscription, $request->days);

        return redirect()->route('billing.index')
            ->with('success', "Trial extended by {$request->days} days.");
    }

    /**
     * Download invoice
     */
    public function downloadInvoice(Order $order)
    {
        $this->authorize('view', $order);

        // TODO: Implement invoice generation
        return response()->json(['message' => 'Invoice generation not implemented yet']);
    }

    /**
     * Get billing history
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $orders = $user->orders()
            ->with(['plan'])
            ->latest()
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($orders);
        }

        return view('billing.history', compact('orders'));
    }

    /**
     * Get upcoming billing
     */
    public function upcoming()
    {
        $user = Auth::user();

        $activeSubscription = $user->activeSubscription;

        if (! $activeSubscription) {
            return response()->json(['message' => 'No active subscription']);
        }

        $nextBillingDate = $activeSubscription->getNextBillingDate();
        $daysUntilBilling = $activeSubscription->getDaysUntilNextBilling();

        return response()->json([
            'next_billing_date' => $nextBillingDate->format('Y-m-d'),
            'days_until_billing' => $daysUntilBilling,
            'amount' => $activeSubscription->amount_cents / 100,
            'currency' => 'USD',
        ]);
    }
}
