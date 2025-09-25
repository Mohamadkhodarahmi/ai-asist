{{-- layouts/navigation.blade.php --}}
@php($user = auth()->user())
<nav class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-lg border-b border-[#e3e3e0]/50 dark:border-[#3E3E3A]/50 sticky top-0 z-50 transition-all duration-300" x-data="{ mobileMenuOpen: false }">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 w-full">
            <!-- Logo Section -->
            <div class="flex items-center gap-3 group">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="relative">
                        <svg class="w-10 h-10 text-[#F53003] dark:text-[#F61500] transition-all duration-300 group-hover:scale-110 group-hover:rotate-12" 
                             viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="24" fill="currentColor" fill-opacity="0.1" 
                                    class="animate-pulse"/>
                            <path d="M12 24L22 34L36 14" stroke="currentColor" stroke-width="3" 
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="absolute -inset-1 bg-gradient-to-r from-[#F53003] to-[#FF4433] rounded-full opacity-0 group-hover:opacity-20 blur transition-all duration-300"></div>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-[#1b1b18] to-[#706f6c] dark:from-[#EDEDEC] dark:to-[#A1A09A] bg-clip-text text-transparent">
                        AI Assistant
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-2">
                <a href="{{ route('dashboard') }}" 
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : null }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 5h16v4H4V5zm0 10h6v4H4v-4zm10 0h6v4h-6v-4z"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('chat') }}" 
                   class="nav-link {{ request()->routeIs('chat') ? 'active' : '' }}" aria-current="{{ request()->routeIs('chat') ? 'page' : null }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0c0-5 4-9 9-9s9 4 9 9z"/>
                    </svg>
                    Chat
                </a>

                <a href="{{ route('pricing') }}" 
                   class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}" aria-current="{{ request()->routeIs('pricing') ? 'page' : null }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm0-6v2m0 12v2M4 12h2m12 0h2"/>
                    </svg>
                    Pricing
                </a>

                <a href="{{ route('docs') }}" 
                   class="nav-link {{ request()->routeIs('docs') ? 'active' : '' }}" aria-current="{{ request()->routeIs('docs') ? 'page' : null }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 20h9M12 4h9m-9 8h9M3 4h.01M3 12h.01M3 20h.01"/>
                    </svg>
                    Docs
                </a>

                <a href="{{ route('community') }}" 
                   class="nav-link {{ request()->routeIs('community') ? 'active' : '' }}" aria-current="{{ request()->routeIs('community') ? 'page' : null }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17 20h5V4H2v16h5m10-4a4 4 0 01-8 0m8 0a4 4 0 00-8 0"/>
                    </svg>
                    Community
                </a>
            </div>

            <!-- Right Side: Upload Button & User Menu -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('upload') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-white bg-gradient-to-r from-[#F53003] to-[#FF4433] hover:shadow-md transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40">
                    Upload
                </a>

                <!-- User Menu Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center space-x-3 px-4 py-2 rounded-xl text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#F53003]/10 dark:hover:bg-[#FF4433]/10 transition-all duration-200 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40"
                            :aria-expanded="open.toString()">
                        <div class="w-8 h-8 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="font-medium">{{ $user->name }}</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" x-transition @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#161615] rounded-xl shadow-xl border border-[#e3e3e0] dark:border-[#3E3E3A] py-2 z-50">
                        <div class="px-4 py-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Signed in as</p>
                            <p class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC] truncate">{{ $user->email }}</p>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-[#F53003] dark:text-[#FF4433] hover:bg-[#F53003]/10 dark:hover:bg-[#FF4433]/10 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#F53003]/10 dark:hover:bg-[#FF4433]/10 transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40" :aria-expanded="mobileMenuOpen.toString()" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="mobileMenuOpen" x-transition x-cloak class="md:hidden bg-white dark:bg-[#161615] border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
        <div class="px-4 py-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : null }}">Dashboard</a>
            <a href="{{ route('chat') }}" class="mobile-nav-link {{ request()->routeIs('chat') ? 'active' : '' }}" aria-current="{{ request()->routeIs('chat') ? 'page' : null }}">Chat</a>
            <a href="{{ route('pricing') }}" class="mobile-nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}" aria-current="{{ request()->routeIs('pricing') ? 'page' : null }}">Pricing</a>
            <a href="{{ route('docs') }}" class="mobile-nav-link {{ request()->routeIs('docs') ? 'active' : '' }}" aria-current="{{ request()->routeIs('docs') ? 'page' : null }}">Docs</a>
            <a href="{{ route('community') }}" class="mobile-nav-link {{ request()->routeIs('community') ? 'active' : '' }}" aria-current="{{ request()->routeIs('community') ? 'page' : null }}">Community</a>
            <a href="{{ route('upload') }}" class="block px-4 py-3 rounded-lg font-semibold   hover:bg-[#c41e00] transition-all">Upload</a>
            <div class="pt-4 mt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-[#F53003] dark:text-[#FF4433] font-medium">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
.nav-link { 
    @apply inline-flex items-center px-3.5 py-2 rounded-lg font-medium text-sm transition-all duration-200 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-[#F53003]/10 dark:hover:bg-[#FF4433]/10 whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40; 
}
.nav-link.active { 
    @apply text-[#F53003] dark:text-[#FF4433] bg-[#F53003]/10 dark:bg-[#FF4433]/10 shadow-sm; 
}
.mobile-nav-link { 
    @apply block px-4 py-3 rounded-lg font-medium transition-all duration-200 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-[#F53003]/10 dark:hover:bg-[#FF4433]/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40; 
}
.mobile-nav-link.active { 
    @apply text-[#F53003] dark:text-[#FF4433] bg-[#F53003]/10 dark:bg-[#FF4433]/10; 
}
[x-cloak] { display: none !important; }
</style>