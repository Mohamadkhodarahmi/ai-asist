<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Services\ApiRateLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ApiKeyController extends Controller
{
    protected ApiRateLimitService $rateLimitService;

    public function __construct(ApiRateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    /**
     * Display the API key management page
     */
    public function index()
    {
        $user = Auth::user();

        if (!$this->rateLimitService->hasApiAccess($user)) {
            abort(403, 'API access is only available for Pro and Business plans. Please upgrade your plan.');
        }

        $apiKeys = ApiKey::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $rateLimits = $this->rateLimitService->getRateLimitsForUser($user);
        $usageStats = $this->rateLimitService->getUsageStats($user, 30);

        return view('api-keys.index', compact('apiKeys', 'rateLimits', 'usageStats'));
    }

    /**
     * Store a newly created API key
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->rateLimitService->hasApiAccess($user)) {
            abort(403, 'API access is only available for Pro and Business plans.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:chat,documents,search',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $apiKey = ApiKey::createForUser(
            $user,
            $request->input('name'),
            $request->input('permissions', ['chat', 'documents', 'search'])
        );

        if ($request->input('expires_at')) {
            $apiKey->update(['expires_at' => $request->input('expires_at')]);
        }

        return redirect()->route('api-keys.index')
            ->with('success', 'API key created successfully. Please save it securely - it will not be shown again.')
            ->with('new_api_key', $apiKey->key);
    }

    /**
     * Update the specified API key
     */
    public function update(Request $request, ApiKey $apiKey)
    {
        $user = Auth::user();

        if ($apiKey->user_id !== $user->id) {
            abort(403);
        }

        if (!$this->rateLimitService->hasApiAccess($user)) {
            abort(403, 'API access is only available for Pro and Business plans.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:chat,documents,search',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'boolean',
        ]);

        $apiKey->update([
            'name' => $request->input('name'),
            'permissions' => $request->input('permissions', $apiKey->permissions),
            'expires_at' => $request->input('expires_at'),
            'is_active' => $request->boolean('is_active', $apiKey->is_active),
        ]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API key updated successfully.');
    }

    /**
     * Remove the specified API key
     */
    public function destroy(ApiKey $apiKey)
    {
        $user = Auth::user();

        if ($apiKey->user_id !== $user->id) {
            abort(403);
        }

        $apiKey->delete();

        return redirect()->route('api-keys.index')
            ->with('success', 'API key deleted successfully.');
    }

    /**
     * Regenerate an API key
     */
    public function regenerate(ApiKey $apiKey)
    {
        $user = Auth::user();

        if ($apiKey->user_id !== $user->id) {
            abort(403);
        }

        if (!$this->rateLimitService->hasApiAccess($user)) {
            abort(403, 'API access is only available for Pro and Business plans.');
        }

        $oldKey = $apiKey->key;
        $apiKey->update(['key' => ApiKey::generateKey()]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API key regenerated successfully. Please update your applications with the new key.')
            ->with('new_api_key', $apiKey->key)
            ->with('old_api_key', $oldKey);
    }
}