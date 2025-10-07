<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService
    ) {}

    /**
     * Display the admin analytics dashboard
     */
    public function index(Request $request): View
    {
        $days = $request->get('days', 30);
        $analytics = $this->analyticsService->getPlatformAnalytics($days);

        return view('admin.analytics.index', compact('analytics', 'days'));
    }

    /**
     * Get analytics data as JSON for AJAX requests
     */
    public function data(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $analytics = $this->analyticsService->getPlatformAnalytics($days);

        return response()->json($analytics);
    }

    /**
     * Get user metrics
     */
    public function users(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        $metrics = $this->analyticsService->getUserMetrics($startDate);

        return response()->json($metrics);
    }

    /**
     * Get revenue metrics
     */
    public function revenue(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        $metrics = $this->analyticsService->getRevenueMetrics($startDate);

        return response()->json($metrics);
    }

    /**
     * Get engagement metrics
     */
    public function engagement(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        $metrics = $this->analyticsService->getEngagementMetrics($startDate);

        return response()->json($metrics);
    }

    /**
     * Get growth metrics
     */
    public function growth(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        $metrics = $this->analyticsService->getGrowthMetrics($startDate);

        return response()->json($metrics);
    }

    /**
     * Get usage metrics
     */
    public function usage(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        $metrics = $this->analyticsService->getUsageMetrics($startDate);

        return response()->json($metrics);
    }

    /**
     * Get cohort analysis
     */
    public function cohorts(): JsonResponse
    {
        $cohorts = $this->analyticsService->getCohortAnalysis();
        return response()->json($cohorts);
    }

    /**
     * Get top features
     */
    public function features(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $features = $this->analyticsService->getTopFeatures($days);
        return response()->json($features);
    }

    /**
     * Export analytics data
     */
    public function export(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $format = $request->get('format', 'json');
        
        $analytics = $this->analyticsService->getPlatformAnalytics($days);
        
        if ($format === 'csv') {
            // Convert to CSV format
            $csvData = $this->convertToCsv($analytics);
            return response()->json(['csv' => $csvData]);
        }

        return response()->json($analytics);
    }

    /**
     * Convert analytics data to CSV format
     */
    private function convertToCsv(array $analytics): string
    {
        $csv = "Metric,Value\n";
        
        // Users metrics
        $csv .= "Total Users,{$analytics['users']['total_users']}\n";
        $csv .= "New Users,{$analytics['users']['new_users']}\n";
        $csv .= "Active Users,{$analytics['users']['active_users']}\n";
        $csv .= "Activation Rate,{$analytics['users']['activation_rate']}%\n";
        
        // Revenue metrics
        $csv .= "MRR,\${$analytics['revenue']['mrr']}\n";
        $csv .= "Total Revenue,\${$analytics['revenue']['total_revenue']}\n";
        $csv .= "ARPU,\${$analytics['revenue']['arpu']}\n";
        $csv .= "CLV,\${$analytics['revenue']['clv']}\n";
        $csv .= "Paying Users,{$analytics['revenue']['paying_users']}\n";
        
        // Growth metrics
        $csv .= "User Growth Rate,{$analytics['growth']['user_growth_rate']}%\n";
        $csv .= "Revenue Growth Rate,{$analytics['growth']['revenue_growth_rate']}%\n";
        $csv .= "Churn Rate,{$analytics['growth']['churn_rate']}%\n";
        
        // Usage metrics
        $csv .= "Total API Calls,{$analytics['usage']['total_api_calls']}\n";
        $csv .= "Avg Response Time,{$analytics['usage']['avg_response_time_ms']}ms\n";
        $csv .= "Total Documents,{$analytics['usage']['total_documents']}\n";
        $csv .= "Document Questions,{$analytics['usage']['total_document_questions']}\n";

        return $csv;
    }
}