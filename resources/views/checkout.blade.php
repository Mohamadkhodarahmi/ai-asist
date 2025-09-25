<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
    @include('layouts.navigation')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white/60 dark:bg-[#161615]/60 backdrop-blur p-6">
            <h1 class="text-2xl font-semibold mb-4">Checkout</h1>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="text-lg font-medium">{{ $plan->name }}</div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">@if($plan->price_cents) ${{ number_format($plan->price_cents / 100, 2) }} / mo @else Free @endif</div>
                </div>
                <div>
                    <a href="{{ route('pricing') }}" class="text-sm underline">Change plan</a>
                </div>
            </div>
            <form method="POST" action="{{ route('checkout.pay') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">You will be redirected to NOWPayments to complete your crypto payment securely.</p>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl font-semibold text-white bg-gradient-to-r from-[#F53003] to-[#FF4433] hover:shadow-md transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40">
                    Pay and Activate
                </button>
            </form>
        </div>
    </div>
</body>
</html>

