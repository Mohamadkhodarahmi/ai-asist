@extends('admin.layout')

@section('title', 'System Health')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-[#1b1b18] to-[#706f6c] dark:from-[#EDEDEC] dark:to-[#A1A09A] bg-clip-text text-transparent mb-2">
                    ⚙️ System Health
                </h1>
                <p class="text-lg text-[#706f6c] dark:text-[#A1A09A]">
                    Monitor system status and performance
                </p>
            </div>
            <button onclick="clearCache()" class="px-6 py-3 bg-gradient-to-r from-[#F53003] to-[#FF4433] text-white rounded-xl hover:shadow-lg hover:shadow-[#F53003]/30 transition-all duration-200 font-medium">
                🗑️ Clear Cache
            </button>
        </div>
    </div>

    <!-- Health Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @foreach($health as $component => $status)
            <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ ucfirst($component) }}</h3>
                    <div class="w-10 h-10 {{ $status['status'] === 'healthy' ? 'bg-green-500' : ($status['status'] === 'warning' ? 'bg-yellow-500' : 'bg-red-500') }} rounded-xl flex items-center justify-center text-white text-xl">
                        {{ $status['icon'] }}
                    </div>
                </div>
                <div class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-1">
                    {{ ucfirst($status['status']) }}
                </div>
                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    {{ $status['message'] }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- System Information -->
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 mb-8">
        <h3 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">System Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($systemInfo as $key => $value)
                <div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-1">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                    </div>
                    <div class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                        @if(is_bool($value))
                            <span class="px-3 py-1 {{ $value ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} rounded-full text-sm">
                                {{ $value ? 'Enabled' : 'Disabled' }}
                            </span>
                        @else
                            {{ $value }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6">
        <h3 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button onclick="refreshHealth()" class="p-4 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded-xl transition-colors text-left">
                <div class="font-medium text-blue-800 dark:text-blue-300 mb-1">🔄 Refresh Health Status</div>
                <div class="text-sm text-blue-600 dark:text-blue-400">Check current system status</div>
            </button>
            <button onclick="clearCache()" class="p-4 bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 dark:hover:bg-purple-900/50 rounded-xl transition-colors text-left">
                <div class="font-medium text-purple-800 dark:text-purple-300 mb-1">🗑️ Clear All Caches</div>
                <div class="text-sm text-purple-600 dark:text-purple-400">Reset application cache</div>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function clearCache() {
    if (!confirm('Are you sure you want to clear all caches?')) {
        return;
    }

    fetch('/admin/system/clear-cache', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message);
            window.location.reload();
        } else {
            alert('✗ ' + data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while clearing cache');
        console.error('Error:', error);
    });
}

function refreshHealth() {
    fetch('/admin/system/health')
        .then(response => response.json())
        .then(data => {
            alert('System health status refreshed!');
            window.location.reload();
        })
        .catch(error => {
            alert('An error occurred while refreshing health status');
            console.error('Error:', error);
        });
}
</script>
@endpush
@endsection

