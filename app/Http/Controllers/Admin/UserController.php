<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display list of all users
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 15);

        $users = User::query()
            ->with(['plan', 'business'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $stats = [
            'total' => User::count(),
            'admins' => User::where('is_admin', true)->count(),
            'active' => User::whereHas('chatAnalytics', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'search'));
    }

    /**
     * Show user details
     */
    public function show(User $user): View
    {
        $user->load(['plan', 'business', 'orders', 'chatAnalytics', 'documentAnalytics']);

        $chatStats = [
            'total_chats' => $user->chatAnalytics()->count(),
            'last_30_days' => $user->chatAnalytics()->where('created_at', '>=', now()->subDays(30))->count(),
            'avg_response_time' => $user->chatAnalytics()->whereNotNull('response_time_ms')->avg('response_time_ms'),
        ];

        $documentStats = [
            'total_documents' => $user->documentAnalytics()->count(),
            'total_questions' => $user->documentAnalytics()->sum('questions_asked'),
        ];

        return view('admin.users.show', compact('user', 'chatStats', 'documentStats'));
    }

    /**
     * Toggle admin status
     */
    public function toggleAdmin(User $user): JsonResponse
    {
        $user->is_admin = ! $user->is_admin;
        $user->save();

        return response()->json([
            'success' => true,
            'is_admin' => $user->is_admin,
            'message' => $user->is_admin ? 'Admin privileges granted' : 'Admin privileges revoked',
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the last admin user',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Update user details
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'plan_id' => 'sometimes|nullable|exists:plans,id',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user->fresh(['plan']),
        ]);
    }

    /**
     * Get user statistics as JSON
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'active_users' => User::whereHas('chatAnalytics', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'users_with_plans' => User::whereNotNull('plan_id')->count(),
            'new_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            'new_this_month' => User::where('created_at', '>=', now()->subMonth())->count(),
        ];

        return response()->json($stats);
    }
}
