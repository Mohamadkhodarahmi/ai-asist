<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\ConversationExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function __construct(
        private ConversationExportService $exportService,
        private AnalyticsService $analyticsService
    ) {}

    public function exportConversations(Request $request)
    {
        $user = Auth::user();

        // Check if user has access to export feature (Starter+ plans)
        if (! $this->hasExportAccess($user)) {
            abort(403, 'Conversation export is only available for Starter, Pro, Business and Enterprise plans.');
        }

        $request->validate([
            'format' => 'required|in:csv,json,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        try {
            $filename = $this->exportService->exportConversations(
                $user,
                $request->input('format'),
                $request->input('date_from'),
                $request->input('date_to')
            );

            // Track export activity
            $this->analyticsService->trackUserActivity($user, 'export', [
                'format' => $request->input('format'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
                'file_size' => filesize($filename),
            ]);

            // Return file download
            return response()->download($filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: '.$e->getMessage());
        }
    }

    public function getExportStats()
    {
        $user = Auth::user();

        // Check if user has access to export feature (Starter+ plans)
        if (! $this->hasExportAccess($user)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $stats = $this->exportService->getExportStats($user);

        return response()->json($stats);
    }

    private function hasExportAccess($user): bool
    {
        $planSlug = $user->plan?->slug;

        return in_array($planSlug, ['starter', 'pro', 'business', 'enterprise']);
    }
}
