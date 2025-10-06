<?php

namespace App\Livewire;

use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class AnalyticsDashboard extends Component
{
    public int $daysFilter = 30;

    public array $chatAnalytics = [];

    public array $documentAnalytics = [];

    public array $activityStats = [];

    public function mount(AnalyticsService $analyticsService): void
    {
        $user = Auth::user();

        // Check if user has access to analytics (Pro+ plans)
        if (! $this->hasAnalyticsAccess($user)) {
            abort(403, 'Analytics dashboard is only available for Pro and Business plans.');
        }

        $this->loadAnalytics($analyticsService, $user);
    }

    public function updatedDaysFilter(AnalyticsService $analyticsService): void
    {
        $user = Auth::user();
        $this->loadAnalytics($analyticsService, $user);
    }

    private function loadAnalytics(AnalyticsService $analyticsService, $user): void
    {
        $this->chatAnalytics = $analyticsService->getChatAnalytics($user, $this->daysFilter);
        $this->documentAnalytics = $analyticsService->getDocumentAnalytics($user);
        $this->activityStats = $analyticsService->getUserActivityStats($user, $this->daysFilter);
    }

    private function hasAnalyticsAccess($user): bool
    {
        $planSlug = $user->plan?->slug;

        return in_array($planSlug, ['pro', 'business']);
    }

    public function render()
    {
        return view('livewire.analytics-dashboard');
    }
}
