<?php

namespace App\Livewire;

use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class DocumentAnalyticsDashboard extends Component
{
    public int $daysFilter = 30;

    public array $documentAnalytics = [];

    public array $performanceStats = [];

    public function mount(AnalyticsService $analyticsService): void
    {
        $user = Auth::user();

        // Check if user has access to document analytics (Starter+ plans)
        if (! $this->hasDocumentAnalyticsAccess($user)) {
            abort(403, 'Document Analytics is only available for Starter, Pro and Business plans. Please upgrade your plan.');
        }

        $this->loadAnalytics($analyticsService);
    }

    public function updatedDaysFilter(AnalyticsService $analyticsService): void
    {
        $this->loadAnalytics($analyticsService);
    }

    private function loadAnalytics(AnalyticsService $analyticsService): void
    {
        $user = Auth::user();

        $this->documentAnalytics = $analyticsService->getDocumentAnalytics($user, $this->daysFilter);
        $this->performanceStats = $analyticsService->getDocumentPerformanceStats($user);
    }

    private function hasDocumentAnalyticsAccess($user): bool
    {
        $planSlug = $user->plan?->slug;

        return in_array($planSlug, ['starter', 'pro', 'business', 'enterprise']);
    }

    public function render()
    {
        return view('livewire.document-analytics-dashboard');
    }
}
