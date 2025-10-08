<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatAnalytic;
use App\Models\DocumentAnalytic;
use App\Models\UserActivityAnalytic;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display recent activity logs
     */
    public function index(Request $request): View
    {
        $type = $request->get('type', 'all');
        $perPage = $request->get('per_page', 20);

        $chatLogs = collect();
        $documentLogs = collect();
        $activityLogs = collect();

        if ($type === 'all' || $type === 'chat') {
            $chatLogs = ChatAnalytic::with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit($perPage)
                ->get()
                ->map(function ($log) {
                    return [
                        'type' => 'chat',
                        'user' => $log->user?->name ?? 'Unknown',
                        'user_id' => $log->user_id,
                        'description' => 'Asked: '.\Str::limit($log->question, 50),
                        'details' => [
                            'question' => $log->question,
                            'answer' => \Str::limit($log->answer, 100),
                            'response_time' => $log->response_time_ms.'ms',
                        ],
                        'created_at' => $log->created_at,
                    ];
                });
        }

        if ($type === 'all' || $type === 'documents') {
            $documentLogs = DocumentAnalytic::with(['user'])
                ->orderBy('last_accessed_at', 'desc')
                ->limit($perPage)
                ->get()
                ->map(function ($log) {
                    return [
                        'type' => 'document',
                        'user' => $log->user?->name ?? 'Unknown',
                        'user_id' => $log->user_id,
                        'description' => 'Uploaded: '.$log->document_name,
                        'details' => [
                            'name' => $log->document_name,
                            'type' => $log->document_type,
                            'questions_asked' => $log->questions_asked,
                        ],
                        'created_at' => $log->created_at,
                    ];
                });
        }

        if ($type === 'all' || $type === 'activity') {
            $activityLogs = UserActivityAnalytic::with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit($perPage)
                ->get()
                ->map(function ($log) {
                    return [
                        'type' => 'activity',
                        'user' => $log->user?->name ?? 'Unknown',
                        'user_id' => $log->user_id,
                        'description' => ucfirst(str_replace('_', ' ', $log->activity_type)),
                        'details' => [
                            'activity_type' => $log->activity_type,
                            'ip_address' => $log->ip_address,
                        ],
                        'created_at' => $log->created_at,
                    ];
                });
        }

        $logs = $chatLogs->concat($documentLogs)
            ->concat($activityLogs)
            ->sortByDesc('created_at')
            ->take($perPage);

        $stats = [
            'total_chats' => ChatAnalytic::count(),
            'total_documents' => DocumentAnalytic::count(),
            'total_activities' => UserActivityAnalytic::count(),
            'today_chats' => ChatAnalytic::whereDate('created_at', today())->count(),
        ];

        return view('admin.logs.index', compact('logs', 'stats', 'type'));
    }
}
