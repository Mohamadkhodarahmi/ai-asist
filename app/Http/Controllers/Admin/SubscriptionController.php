<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Show subscription management dashboard
     */
    public function index(Request $request): View
    {
        $query = Subscription::with(['user', 'plan']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->has('plan_id') && $request->plan_id !== '') {
            $query->where('plan_id', $request->plan_id);
        }

        // Search by user
        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $subscriptions = $query->latest()->paginate(20);
        $metrics = $this->subscriptionService->getSubscriptionMetrics(30);

        return view('admin.subscriptions.index', compact('subscriptions', 'metrics'));
    }

    /**
     * Show subscription details
     */
    public function show(Subscription $subscription): View
    {
        $subscription->load(['user', 'plan', 'orders']);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request, Subscription $subscription)
    {
        $request->validate([
            'immediate' => 'boolean',
            'reason' => 'nullable|string|max:500',
        ]);

        $immediate = $request->boolean('immediate', false);
        $this->subscriptionService->cancelSubscription($subscription, $immediate);

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', $immediate ?
                'Subscription cancelled immediately.' :
                'Subscription will be cancelled at the end of the current billing period.');
    }

    /**
     * Resume subscription
     */
    public function resume(Subscription $subscription)
    {
        $this->subscriptionService->resumeSubscription($subscription);

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription resumed successfully.');
    }

    /**
     * Extend trial
     */
    public function extendTrial(Request $request, Subscription $subscription)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:90',
        ]);

        $this->subscriptionService->extendTrial($subscription, $request->days);

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', "Trial extended by {$request->days} days.");
    }

    /**
     * Process dunning management
     */
    public function processDunning()
    {
        $this->subscriptionService->processDunningManagement();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Dunning management processed successfully.');
    }

    /**
     * Get subscription analytics
     */
    public function analytics(Request $request)
    {
        $days = $request->get('days', 30);
        $metrics = $this->subscriptionService->getSubscriptionMetrics($days);

        return response()->json($metrics);
    }

    /**
     * Export subscriptions
     */
    public function export(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->get();

        $csvData = [];
        $csvData[] = ['ID', 'User', 'Email', 'Plan', 'Status', 'Amount', 'Billing Period', 'Created At', 'Next Billing'];

        foreach ($subscriptions as $subscription) {
            $csvData[] = [
                $subscription->id,
                $subscription->user->name,
                $subscription->user->email,
                $subscription->plan->name,
                $subscription->status,
                '$'.number_format($subscription->amount_cents / 100, 2),
                $subscription->billing_period,
                $subscription->created_at->format('Y-m-d'),
                $subscription->getNextBillingDate()->format('Y-m-d'),
            ];
        }

        $filename = 'subscriptions_'.now()->format('Y-m-d_H-i-s').'.csv';

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
