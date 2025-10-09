<div class="py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">🔑 API Key Management</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your API keys for programmatic access to your AI assistant</p>
        </div>

        @if(!$hasAccess)
            {{-- Access Denied --}}
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">API Access Not Available</h3>
                        <p class="text-yellow-700 dark:text-yellow-300 mt-1">
                            API access is only available for Pro and Business plans. 
                            <a href="{{ route('pricing') }}" class="underline hover:text-yellow-900 dark:hover:text-yellow-100">Upgrade your plan</a> to get API access.
                        </p>
                    </div>
                </div>
            </div>
        @else
            {{-- Flash Messages --}}
            @if (session()->has('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                    <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    @if (session()->has('new_api_key'))
                        <div class="mt-3 p-3 bg-green-100 dark:bg-green-900/40 rounded-lg">
                            <p class="text-sm font-medium text-green-900 dark:text-green-100 mb-2">Your new API key:</p>
                            <code class="block text-sm bg-white dark:bg-gray-800 p-2 rounded border font-mono text-gray-900 dark:text-white break-all overflow-x-auto">{{ session('new_api_key') }}</code>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-2">
                                ⚠️ Save this key securely - it will not be shown again!
                            </p>
                        </div>
                    @endif
                    @if (session()->has('old_api_key'))
                        <div class="mt-3 p-3 bg-yellow-100 dark:bg-yellow-900/40 rounded-lg">
                            <p class="text-sm font-medium text-yellow-900 dark:text-yellow-100 mb-2">Old API key (now inactive):</p>
                            <code class="block text-sm bg-white dark:bg-gray-800 p-2 rounded border font-mono text-gray-900 dark:text-white break-all overflow-x-auto">{{ session('old_api_key') }}</code>
                        </div>
                    @endif
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                    <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Rate Limits Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 w-full">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Per Minute</h3>
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rateLimits['requests_per_minute'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">requests allowed</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Per Hour</h3>
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($rateLimits['requests_per_hour'] ?? 0) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">requests allowed</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Per Day</h3>
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0v4a2 2 0 002 2h4a2 2 0 002-2V7m-6 0V3a2 2 0 012-2h4a2 2 0 012 2v4"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($rateLimits['requests_per_day'] ?? 0) }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">requests allowed</p>
                </div>
            </div>

            {{-- Usage Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8 w-full">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Usage Statistics (Last 30 Days)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total API Requests</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($usageStats['total_requests'] ?? 0) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Active API Keys</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ collect($apiKeys)->where('is_active', true)->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- API Keys List --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 w-full">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">API Keys</h3>
                        <button wire:click="toggleCreateForm" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Create New Key
                        </button>
                    </div>
                </div>

                {{-- Create Form --}}
                @if($showCreateForm)
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Create New API Key</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="My API Key">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions</label>
                                <div class="space-y-2">
                                    @foreach($permissionOptions as $key => $label)
                                        <label class="flex items-center">
                                            <input type="checkbox" wire:model="permissions" value="{{ $key }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expires At (Optional)</label>
                                <input type="date" wire:model="expiresAt" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                @error('expiresAt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex gap-3">
                                <button wire:click="createApiKey" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Create Key
                                </button>
                                <button wire:click="toggleCreateForm" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- API Keys List --}}
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($apiKeys as $apiKey)
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $apiKey['name'] }}</h4>
                                        @if($apiKey['is_active'])
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-2 space-y-1">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Key: <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs font-mono break-all">{{ substr($apiKey['key'], 0, 8) }}...{{ substr($apiKey['key'], -4) }}</code>
                                        </p>
                                        
                                        @if($apiKey['permissions'])
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Permissions: 
                                                @foreach($apiKey['permissions'] as $permission)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 mr-1">
                                                        {{ $permissionOptions[$permission] ?? $permission }}
                                                    </span>
                                                @endforeach
                                            </p>
                                        @endif

                                        @if($apiKey['expires_at'])
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Expires: {{ date('M d, Y', strtotime($apiKey['expires_at'])) }}
                                            </p>
                                        @endif

                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Last used: {{ $apiKey['last_used_at'] ? date('M d, Y H:i', strtotime($apiKey['last_used_at'])) : 'Never' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <button wire:click="editApiKey({{ $apiKey['id'] }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                        Edit
                                    </button>
                                    <button wire:click="regenerateApiKey({{ $apiKey['id'] }})" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 text-sm font-medium">
                                        Regenerate
                                    </button>
                                    <button wire:click="deleteApiKey({{ $apiKey['id'] }})" onclick="return confirm('Are you sure you want to delete this API key?')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m0 0H9a2 2 0 00-2 2v2m0 0a2 2 0 00-2 2m0 0v2a2 2 0 002 2m0 0h6a2 2 0 002-2v-2m0 0a2 2 0 00-2-2m0 0V9a2 2 0 00-2 2"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No API keys</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first API key.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- API Documentation --}}
            <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800 w-full">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">📚 API Documentation</h3>
                <div class="space-y-4 text-sm text-blue-800 dark:text-blue-200">
                    <p><strong>Base URL:</strong> <code class="bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded break-all">{{ url('/api/v1') }}</code></p>
                    <p><strong>Authentication:</strong> Include your API key in the <code class="bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded">Authorization</code> header as <code class="bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded break-all">Bearer YOUR_API_KEY</code></p>
                    <p><strong>Available Endpoints:</strong></p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li><code class="bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded break-all">GET /api/v1/test</code> - Test API connectivity</li>
                        <li><code class="bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded break-all">POST /api/v1/chat</code> - Chat with AI</li>
                        <li><code class="bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded break-all">GET /api/v1/documents</code> - List documents</li>
                        <li><code class="bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded break-all">POST /api/v1/search</code> - Search documents</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>