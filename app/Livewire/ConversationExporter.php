<?php

namespace App\Livewire;

use App\Services\AnalyticsService;
use App\Services\ConversationExportService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class ConversationExporter extends Component
{
    public string $format = 'csv';

    public string $dateFrom = '';

    public string $dateTo = '';

    public array $exportStats = [];

    public bool $isExporting = false;

    public array $formatOptions = [
        'csv' => 'CSV (Spreadsheet)',
        'json' => 'JSON (Data)',
        'pdf' => 'PDF (Document)',
    ];

    protected array $rules = [
        'format' => 'required|in:csv,json,pdf',
        'dateFrom' => 'nullable|date',
        'dateTo' => 'nullable|date|after_or_equal:dateFrom',
    ];

    public function mount(ConversationExportService $exportService): void
    {
        $user = Auth::user();

        // Check if user has access to export feature (Starter+ plans)
        if (! $this->hasExportAccess($user)) {
            abort(403, 'Conversation export is only available for Starter, Pro and Business plans.');
        }

        // Load export stats
        $this->exportStats = $exportService->getExportStats($user);

        // Set default date range to last 30 days
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    public function exportConversations(AnalyticsService $analyticsService): void
    {
        $this->validate();

        $this->isExporting = true;

        try {
            // Redirect to export endpoint which will handle the download
            $queryParams = http_build_query([
                'format' => $this->format,
                'date_from' => $this->dateFrom ?: null,
                'date_to' => $this->dateTo ?: null,
            ]);

            // Track analytics
            $analyticsService->trackUserActivity(Auth::user(), 'export_conversations', [
                'format' => $this->format,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ]);

            $this->dispatch('download-export', url: route('export.conversations').'?'.$queryParams);

            session()->flash('message', 'Export started! Your download should begin shortly.');

        } catch (\Exception $e) {
            session()->flash('error', 'Export failed: '.$e->getMessage());
        } finally {
            $this->isExporting = false;
        }
    }

    public function setQuickDateRange(string $range): void
    {
        $this->dateTo = now()->format('Y-m-d');

        match ($range) {
            'week' => $this->dateFrom = now()->subWeek()->format('Y-m-d'),
            'month' => $this->dateFrom = now()->subMonth()->format('Y-m-d'),
            '3months' => $this->dateFrom = now()->subMonths(3)->format('Y-m-d'),
            'year' => $this->dateFrom = now()->subYear()->format('Y-m-d'),
            'all' => $this->dateFrom = '',
            default => null,
        };
    }

    private function hasExportAccess($user): bool
    {
        $planSlug = $user->plan?->slug;

        return in_array($planSlug, ['starter', 'pro', 'business', 'enterprise']);
    }

    public function render()
    {
        return view('livewire.conversation-exporter');
    }
}
