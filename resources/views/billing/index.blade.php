@extends('layouts.app')

@section('title', 'Billing Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Billing Dashboard</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] mt-2">Manage your subscription and billing information</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Current Subscription -->
            <div class="lg:col-span-2">
                <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                    <h2 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">Current Subscription</h2>
                    
                    @if($activeSubscription)
                        <div class="space-y-4">
                            <!-- Subscription Details -->
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-[#F53003]/10 to-[#FF4433]/10 rounded-xl">
                                <div>
                                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
                                        {{ $activeSubscription->plan ? $activeSubscription->plan->name : 'Unknown' }} Plan
                                    </h3>
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                        ${{ number_format($activeSubscription->amount_cents / 100, 2) }} / {{ $activeSubscription->billing_period }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium
                                        @if($activeSubscription->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($activeSubscription->status === 'trialing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($activeSubscription->status === 'past_due') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $activeSubscription->status)) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Billing Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-[#FDFDFC] dark:bg-[#0a0a0a] rounded-xl border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">
                                    <h4 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Next Billing Date</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ $activeSubscription->getNextBillingDate()->format('M j, Y') }}
                                    </p>
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ $activeSubscription->getDaysUntilNextBilling() }} days remaining
                                    </p>
                                </div>
                                
                                <div class="p-4 bg-[#FDFDFC] dark:bg-[#0a0a0a] rounded-xl border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">
                                    <h4 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Billing Period</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ ucfirst($activeSubscription->billing_period) }}
                                    </p>
                                </div>
                            </div>

                            @if($activeSubscription->isOnTrial())
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <p class="text-blue-800 dark:text-blue-200">
                                            Trial ends on {{ $activeSubscription->trial_ends_at->format('M j, Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-3 pt-4">
                                @if($activeSubscription->status === 'active' && !$activeSubscription->isCancelled())
                                    <button onclick="cancelSubscription({{ $activeSubscription->id }})" 
                                            class="px-4 py-2 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors">
                                        Cancel Subscription
                                    </button>
                                @endif

                                @if($activeSubscription->isCancelled())
                                    <button onclick="resumeSubscription({{ $activeSubscription->id }})" 
                                            class="px-4 py-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-lg hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                        Resume Subscription
                                    </button>
                                @endif

                                @if($activeSubscription->isOnTrial())
                                    <button onclick="extendTrial({{ $activeSubscription->id }})" 
                                            class="px-4 py-2 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                        Extend Trial
                                    </button>
                                @endif

                                <a href="{{ route('billing.change-plan', $activeSubscription) }}" 
                                   class="px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#FF4433] transition-colors">
                                    Change Plan
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-[#706f6c] dark:text-[#A1A09A] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">No Active Subscription</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">You're currently on the free plan.</p>
                            <a href="{{ route('pricing') }}" 
                               class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#FF4433] transition-colors">
                                View Plans
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('billing.history') }}" 
                           class="flex items-center p-3 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-[#F53003]/10 dark:hover:bg-[#FF4433]/10 rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Billing History
                        </a>
                        <a href="{{ route('pricing') }}" 
                           class="flex items-center p-3 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-[#F53003]/10 dark:hover:bg-[#FF4433]/10 rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            View Plans
                        </a>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Recent Orders</h3>
                    <div class="space-y-3">
                        @forelse($orders->take(5) as $order)
                            <div class="flex items-center justify-between p-3 bg-[#FDFDFC] dark:bg-[#0a0a0a] rounded-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">
                                <div>
                                    <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                        {{ $order->plan ? $order->plan->name : 'Unknown Plan' }}
                                    </p>
                                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ $order->created_at->format('M j, Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                        ${{ number_format($order->amount / 100, 2) }}
                                    </p>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                        @if($order->status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($order->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">No orders yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Subscription Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-[#161615] rounded-2xl p-6 max-w-md w-full">
                <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Cancel Subscription</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] mb-6">Are you sure you want to cancel your subscription?</p>
                <div class="flex gap-3">
                    <button onclick="closeCancelModal()" 
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Keep Subscription
                    </button>
                    <button onclick="confirmCancel()" 
                            class="flex-1 px-4 py-2 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors">
                        Cancel Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSubscriptionId = null;

        function cancelSubscription(subscriptionId) {
            currentSubscriptionId = subscriptionId;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            currentSubscriptionId = null;
        }

        function confirmCancel() {
            if (currentSubscriptionId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/billing/subscription/${currentSubscriptionId}/cancel`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const immediateField = document.createElement('input');
                immediateField.type = 'hidden';
                immediateField.name = 'immediate';
                immediateField.value = 'true';
                
                form.appendChild(csrfToken);
                form.appendChild(immediateField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function resumeSubscription(subscriptionId) {
            if (confirm('Are you sure you want to resume your subscription?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/billing/subscription/${subscriptionId}/resume`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function extendTrial(subscriptionId) {
            const days = prompt('How many days would you like to extend the trial? (1-30)');
            if (days && days >= 1 && days <= 30) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/billing/subscription/${subscriptionId}/extend-trial`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const daysField = document.createElement('input');
                daysField.type = 'hidden';
                daysField.name = 'days';
                daysField.value = days;
                
                form.appendChild(csrfToken);
                form.appendChild(daysField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
