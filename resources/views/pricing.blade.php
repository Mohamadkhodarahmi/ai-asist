<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 48, 3, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(245, 48, 3, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 48, 3, 0);
            }
        }
        
        @keyframes badge-bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }
        
        @keyframes gradient-shift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        @keyframes shimmer-sweep {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        .animate-shimmer {
            animation: shimmer-sweep 3s infinite;
        }
        
        .btn-shimmer {
            position: relative;
            background-size: 200% 100%;
        }
        
        .btn-shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
            z-index: 1;
        }
        
        .btn-shimmer:hover::before {
            left: 100%;
            animation: shimmer 0.75s ease-in-out;
        }
        
        .btn-shimmer:active {
            box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .pulse-ring-animation {
            animation: pulse-ring 2s infinite;
        }
        
        .badge-bounce {
            animation: badge-bounce 2s ease-in-out infinite;
        }
        
        /* Enhanced button 3D effect */
        button[type="submit"]:not(:disabled),
        a[href] {
            box-shadow: 
                0 4px 6px -1px rgba(245, 48, 3, 0.3),
                0 2px 4px -1px rgba(245, 48, 3, 0.2),
                inset 0 -2px 0 rgba(0, 0, 0, 0.2);
        }
        
        button[type="submit"]:not(:disabled):hover,
        a[href]:hover {
            box-shadow: 
                0 20px 25px -5px rgba(245, 48, 3, 0.4),
                0 10px 10px -5px rgba(245, 48, 3, 0.3),
                inset 0 -2px 0 rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-4xl font-bold mb-12 text-center">Choose Your Plan</h1>

        {{-- Pricing Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @foreach($plans as $plan)
                @php
                    $isRecommended = $loop->iteration === 2 && $plans->count() >= 3;
                    $isCurrentPlan = auth()->check() && auth()->user()->plan?->id === $plan->id;
                @endphp
                
                {{-- Card Wrapper with extra space for badge --}}
                <div class="@if($isRecommended) pt-6 @endif">
                    <div class="relative rounded-2xl p-8 flex flex-col border transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl
                                @if($isRecommended)
                                    border-[#F53003] shadow-xl shadow-[#F53003]/20 dark:bg-[#1a1a1a] lg:scale-105
                                @else
                                    border-[#e3e3e0] dark:border-[#2a2a2a] dark:bg-[#141414] hover:border-[#F53003]
                                @endif">

                        {{-- Recommended Badge --}}
                        @if($isRecommended)
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 z-20 w-full flex justify-center">
                                <div class="relative">
                                    <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-extrabold uppercase tracking-wider
                                                bg-gradient-to-r from-[#F53003] to-[#ff4422] 
                                                text-white 
                                                shadow-[0_4px_14px_0_rgba(245,48,3,0.5)]
                                                border border-[#ff6644]
                                                relative overflow-hidden
                                                whitespace-nowrap">
                                        {{-- Shine effect --}}
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                        
                                        <svg class="w-3 h-3 relative z-10 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="relative z-10">Most Popular</span>
                                    </div>
                                    {{-- Enhanced glow --}}
                                    <div class="absolute -inset-1 bg-gradient-to-r from-[#F53003] to-[#ff4422] rounded-lg blur opacity-75 -z-10 animate-pulse"></div>
                                </div>
                            </div>
                        @endif

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
                                        class="btn-shimmer group relative w-full px-6 py-4 rounded-xl font-bold text-base text-white 
                                               bg-gradient-to-br from-[#F53003] via-[#ff3311] to-[#ff4422] 
                                               hover:from-[#ff4422] hover:via-[#ff3311] hover:to-[#F53003]
                                               shadow-lg shadow-[#F53003]/30
                                               transition-all duration-300 ease-in-out
                                               hover:scale-105 hover:shadow-2xl hover:shadow-[#F53003]/50
                                               active:scale-95
                                               focus:outline-none focus:ring-4 focus:ring-[#F53003]/50
                                               transform
                                               overflow-hidden">
                                        <span class="relative z-10 flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Buy {{ $plan->name }}
                                        </span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/30 to-white/0 
                                                    opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                                    transform -skew-x-12"></div>
                                    </button>
                                </form>
                            @else
                                {{-- Free Plan Button --}}
                                <form method="POST" action="{{ route('plan.select') }}" class="w-full">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button type="submit"
                                            @if($isCurrentPlan) disabled @endif
                                            class="btn-shimmer group relative w-full px-6 py-4 rounded-xl font-bold text-base text-white 
                                                   @if($isCurrentPlan)
                                                       bg-gradient-to-br from-gray-500 to-gray-600
                                                       shadow-md cursor-not-allowed opacity-70
                                                   @else
                                                       bg-gradient-to-br from-[#F53003] via-[#ff3311] to-[#ff4422]
                                                       hover:from-[#ff4422] hover:via-[#ff3311] hover:to-[#F53003]
                                                       shadow-lg shadow-[#F53003]/30
                                                       hover:scale-105 hover:shadow-2xl hover:shadow-[#F53003]/50
                                                       active:scale-95
                                                       focus:ring-4 focus:ring-[#F53003]/50
                                                       transform
                                                   @endif
                                                   transition-all duration-300 ease-in-out
                                                   focus:outline-none
                                                   overflow-hidden">
                                        <span class="relative z-10 flex items-center justify-center gap-2">
                                            @if($isCurrentPlan)
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Current Plan
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Choose {{ $plan->name }}
                                            @endif
                                        </span>
                                        @if(!$isCurrentPlan)
                                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/30 to-white/0 
                                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                                        transform -skew-x-12"></div>
                                        @endif
                                    </button>
                                </form>
                            @endif
                        @else
                            {{-- Not Logged In --}}
                            <a href="{{ route('login') }}" 
                               class="btn-shimmer group relative w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl font-bold text-base text-white 
                                      bg-gradient-to-br from-[#F53003] via-[#ff3311] to-[#ff4422] 
                                      hover:from-[#ff4422] hover:via-[#ff3311] hover:to-[#F53003]
                                      shadow-lg shadow-[#F53003]/30
                                      transition-all duration-300 ease-in-out
                                      hover:scale-105 hover:shadow-2xl hover:shadow-[#F53003]/50
                                      active:scale-95
                                      focus:outline-none focus:ring-4 focus:ring-[#F53003]/50
                                      transform
                                      overflow-hidden">
                                <span class="relative z-10 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign in to choose
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/30 to-white/0 
                                            opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                            transform -skew-x-12"></div>
                            </a>
                        @endauth
                    </div>
                </div>
                </div>
            @endforeach
        </div>
    </div>
    
    @livewireScripts
</body>
</html>
