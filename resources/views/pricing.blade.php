@php($plans = \App\Models\Plan::query()->orderBy('price_cents')->get())
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
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-3">Choose your plan</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A]">Upgrade anytime. Free plan includes 5 messages per day.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white/60 dark:bg-[#161615]/60 backdrop-blur p-6 flex flex-col">
                    <div class="mb-4">
                        <h2 class="text-2xl font-semibold">{{ $plan->name }}</h2>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">@if($plan->price_cents)
                            ${{ number_format($plan->price_cents / 100, 2) }} / mo
                        @else
                            Free
                        @endif</p>
                    </div>
                    <ul class="space-y-2 mb-6">
                        @foreach((array)($plan->features ?? []) as $feature)
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#F53003]"></span> {{ $feature }}</li>
                        @endforeach
                    </ul>
                    @auth
                        <form method="POST" action="{{ route('plan.select') }}" class="mt-auto">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl font-semibold text-white bg-gradient-to-r from-[#F53003] to-[#FF4433] hover:shadow-md transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40">
                                @if(auth()->user()->plan?->id === $plan->id)
                                    Current plan
                                @else
                                    Choose {{ $plan->name }}
                                @endif
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="mt-auto w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl font-semibold text-white bg-gradient-to-r from-[#F53003] to-[#FF4433] hover:shadow-md transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40">
                            Sign in to choose
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>


