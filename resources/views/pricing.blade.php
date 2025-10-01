<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-4xl font-bold mb-12 text-center">Choose Your Plan</h1>

        {{-- Pricing Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
            @foreach($plans as $plan)
                @php
                    $isRecommended = $loop->iteration === 2 && $plans->count() >= 3;
                    $isCurrentPlan = auth()->check() && auth()->user()->plan?->id === $plan->id;
                @endphp
                
                <div class="relative rounded-2xl p-8 flex flex-col border transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl
                            @if($isRecommended)
                                border-[#F53003] shadow-xl shadow-[#F53003]/20 dark:bg-[#1a1a1a] lg:scale-105
                            @else
                                border-[#e3e3e0] dark:border-[#2a2a2a] dark:bg-[#141414] hover:border-[#F53003]
                            @endif">

                    {{-- Plan Name --}}
                    <h2 class="text-2xl font-bold mb-4">{{ $plan->name }}</h2>

                    {{-- Price --}}
                    <div class="mb-6">
                        @if($plan->price_cents)
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-bold">${{ number_format($plan->price_cents / 100, 2) }}</span>
                                <span class="text-base text-[#706f6c] dark:text-[#888]">/ month</span>
                            </div>
                        @else
                            <span class="text-5xl font-bold">Free</span>
                        @endif
                    </div>

                    {{-- Features List --}}
                    <ul class="space-y-4 mb-8 flex-grow">
                        @foreach((array)($plan->features ?? []) as $feature)
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#F53003] shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-[#706f6c] dark:text-[#ccc]">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- CTA Button --}}
                    <div class="mt-auto">
                        @auth
                            @if(($plan->price_cents ?? 0) > 0)
                                {{-- Paid Plan Button --}}
                                <form method="GET" action="{{ route('checkout.create') }}" class="w-full">
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button type="submit" 
                                        class="w-full px-6 py-3.5 rounded-xl font-semibold text-white 
                                               bg-red-600 hover:bg-red-700 
                                               transition-all duration-200 
                                               hover:scale-105 hover:shadow-lg hover:shadow-[#F53003]/30
                                               focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:ring-offset-2">
                                        Buy {{ $plan->name }}
                                    </button>
                                </form>
                            @else
                                {{-- Free Plan Button --}}
                                <form method="POST" action="{{ route('plan.select') }}" class="w-full">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button type="submit"
                                            @if($isCurrentPlan) disabled @endif
                                            class="w-full px-6 py-3.5 rounded-xl font-semibold text-white 
                                                   bg-red-600 hover:bg-red-700 
                                                   transition-all duration-200 
                                                   hover:scale-105 hover:shadow-lg hover:shadow-[#F53003]/30
                                                   focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:ring-offset-2
                                                   disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-600 disabled:hover:shadow-none">
                                        @if($isCurrentPlan)
                                            Current plan
                                        @else
                                            Choose {{ $plan->name }}
                                        @endif
                                    </button>
                                </form>
                            @endif
                        @else
                            {{-- Not Logged In --}}
                            <a href="{{ route('login') }}" 
                               class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-xl font-semibold text-white 
                                      bg-[#F53003] hover:bg-[#ff4422] 
                                      transition-all duration-200 
                                      hover:scale-105 hover:shadow-lg hover:shadow-[#F53003]/30
                                      focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:ring-offset-2">
                                Sign in to choose
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
