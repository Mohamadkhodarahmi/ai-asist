<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SystemController extends Controller
{
    /**
     * Display system health dashboard
     */
    public function index(): View
    {
        $health = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
        ];

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'database_driver' => config('database.default'),
        ];

        $recentErrors = $this->getRecentErrors();

        return view('admin.system.index', compact('health', 'systemInfo', 'recentErrors'));
    }

    /**
     * Get system health as JSON
     */
    public function health(): JsonResponse
    {
        $health = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
        ];

        return response()->json($health);
    }

    /**
     * Clear application cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check database connectivity
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            $userCount = User::count();

            return [
                'status' => 'healthy',
                'message' => "Connected ({$userCount} users)",
                'icon' => '✓',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Connection failed',
                'icon' => '✗',
            ];
        }
    }

    /**
     * Check storage availability
     */
    private function checkStorage(): array
    {
        try {
            $storagePath = storage_path();
            $freeSpace = disk_free_space($storagePath);
            $totalSpace = disk_total_space($storagePath);
            $usedPercent = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);

            return [
                'status' => $usedPercent < 90 ? 'healthy' : 'warning',
                'message' => "{$usedPercent}% used",
                'icon' => $usedPercent < 90 ? '✓' : '⚠',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Unable to check',
                'icon' => '✗',
            ];
        }
    }

    /**
     * Check cache connectivity
     */
    private function checkCache(): array
    {
        try {
            $key = 'health_check_'.time();
            cache()->put($key, 'test', 60);
            $value = cache()->get($key);
            cache()->forget($key);

            return [
                'status' => $value === 'test' ? 'healthy' : 'warning',
                'message' => $value === 'test' ? 'Working' : 'Not working',
                'icon' => $value === 'test' ? '✓' : '⚠',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Connection failed',
                'icon' => '✗',
            ];
        }
    }

    /**
     * Check queue status
     */
    private function checkQueue(): array
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();
            $queuedJobs = DB::table('jobs')->count();

            return [
                'status' => $failedJobs < 10 ? 'healthy' : 'warning',
                'message' => "{$queuedJobs} queued, {$failedJobs} failed",
                'icon' => $failedJobs < 10 ? '✓' : '⚠',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'message' => 'Unable to check',
                'icon' => '?',
            ];
        }
    }

    /**
     * Get recent error logs (simplified)
     */
    private function getRecentErrors(): array
    {
        // This is a simplified version - you might want to read from actual log files
        return [];
    }
}
