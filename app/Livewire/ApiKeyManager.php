<?php

namespace App\Livewire;

use App\Models\ApiKey;
use App\Services\ApiRateLimitService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.api-layout')]
class ApiKeyManager extends Component
{
    public $apiKeys = [];
    public $rateLimits = [];
    public $usageStats = [];
    public $hasAccess = false;

    // Form fields
    public $showCreateForm = false;
    public $editingId = null;
    public $name = '';
    public $permissions = [];
    public $expiresAt = '';
    public $isActive = true;

    public $permissionOptions = [
        'chat' => 'Chat with AI',
        'documents' => 'Access Documents',
        'search' => 'Search Documents',
    ];

    protected ApiRateLimitService $rateLimitService;

    public function boot(ApiRateLimitService $rateLimitService)
    {
        $this->rateLimitService = $rateLimitService;
    }

    public function mount()
    {
        $user = Auth::user();
        $this->hasAccess = $this->rateLimitService->hasApiAccess($user);

        if ($this->hasAccess) {
            $this->loadData();
        }
    }

    public function loadData()
    {
        $user = Auth::user();
        
        $this->apiKeys = ApiKey::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $this->rateLimits = $this->rateLimitService->getRateLimitsForUser($user);
        $this->usageStats = $this->rateLimitService->getUsageStats($user, 30);
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->resetForm();
    }

    public function createApiKey()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:chat,documents,search',
            'expiresAt' => 'nullable|date|after:now',
        ]);

        $user = Auth::user();
        $apiKey = ApiKey::createForUser(
            $user,
            $this->name,
            $this->permissions ?: ['chat', 'documents', 'search']
        );

        if ($this->expiresAt) {
            $apiKey->update(['expires_at' => $this->expiresAt]);
        }

        $this->resetForm();
        $this->showCreateForm = false;
        $this->loadData();

        session()->flash('success', 'API key created successfully!');
        session()->flash('new_api_key', $apiKey->key);
    }

    public function editApiKey($id)
    {
        $apiKey = collect($this->apiKeys)->firstWhere('id', $id);
        
        if (!$apiKey) {
            return;
        }

        $this->editingId = $id;
        $this->name = $apiKey['name'];
        $this->permissions = $apiKey['permissions'] ?: [];
        $this->expiresAt = $apiKey['expires_at'] ? date('Y-m-d', strtotime($apiKey['expires_at'])) : '';
        $this->isActive = $apiKey['is_active'];
    }

    public function updateApiKey()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:chat,documents,search',
            'expiresAt' => 'nullable|date|after:now',
        ]);

        $apiKey = ApiKey::find($this->editingId);
        
        if (!$apiKey || $apiKey->user_id !== Auth::id()) {
            session()->flash('error', 'API key not found.');
            return;
        }

        $apiKey->update([
            'name' => $this->name,
            'permissions' => $this->permissions ?: $apiKey->permissions,
            'expires_at' => $this->expiresAt ?: null,
            'is_active' => $this->isActive,
        ]);

        $this->resetForm();
        $this->loadData();

        session()->flash('success', 'API key updated successfully.');
    }

    public function deleteApiKey($id)
    {
        $apiKey = ApiKey::find($id);
        
        if (!$apiKey || $apiKey->user_id !== Auth::id()) {
            session()->flash('error', 'API key not found.');
            return;
        }

        $apiKey->delete();
        $this->loadData();

        session()->flash('success', 'API key deleted successfully.');
    }

    public function regenerateApiKey($id)
    {
        $apiKey = ApiKey::find($id);
        
        if (!$apiKey || $apiKey->user_id !== Auth::id()) {
            session()->flash('error', 'API key not found.');
            return;
        }

        $oldKey = $apiKey->key;
        $apiKey->update(['key' => ApiKey::generateKey()]);
        
        $this->loadData();

        session()->flash('success', 'API key regenerated successfully!');
        session()->flash('new_api_key', $apiKey->key);
        session()->flash('old_api_key', $oldKey);
    }

    public function togglePermission($permission)
    {
        if (in_array($permission, $this->permissions)) {
            $this->permissions = array_filter($this->permissions, fn($p) => $p !== $permission);
        } else {
            $this->permissions[] = $permission;
        }
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->permissions = [];
        $this->expiresAt = '';
        $this->isActive = true;
    }

    public function render()
    {
        return view('livewire.api-key-manager');
    }
}