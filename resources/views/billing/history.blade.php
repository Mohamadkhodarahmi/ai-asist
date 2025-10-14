@extends('layouts.app')

@section('title', 'Billing History')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Billing History</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] mt-2">View your payment history and invoices</p>
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

    <!-- Billing History Table -->
    <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">
            <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Payment History</h2>
        </div>

        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#FDFDFC] dark:bg-[#0a0a0a] border-b border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e3e0]/50 dark:divide-[#3E3E3A]/50">
                        @foreach($orders as $order)
                            <tr class="hover:bg-[#FDFDFC]/50 dark:hover:bg-[#0a0a0a]/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                    #{{ $order->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $order->plan ? $order->plan->name : 'Unknown Plan' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                    ${{ number_format($order->amount / 100, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($order->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($order->status === 'refunded') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $order->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    @if($order->status === 'paid')
                                        <a href="{{ route('billing.download-invoice', $order) }}" 
                                           class="text-[#F53003] dark:text-[#FF4433] hover:text-[#FF4433] dark:hover:text-[#F53003] font-medium">
                                            Download Invoice
                                        </a>
                                    @else
                                        <span class="text-[#706f6c] dark:text-[#A1A09A]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 text-[#706f6c] dark:text-[#A1A09A] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">No Orders Found</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">You haven't made any purchases yet.</p>
                <a href="{{ route('pricing') }}" 
                   class="inline-flex items-center px-4 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#FF4433] transition-colors">
                    View Plans
                </a>
            </div>
        @endif
    </div>

    <!-- Back to Billing -->
    <div class="mt-8">
        <a href="{{ route('billing.index') }}" 
           class="inline-flex items-center px-4 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Billing Dashboard
        </a>
    </div>
</div>
@endsection




