<nav style="background-color: #f3f4f6; padding: 1rem; border-bottom: 1px solid #e5e7eb;">
    <div style="width: 80%; margin: auto; display: flex; justify-content: space-between; align-items: center;">

        {{-- App Name / Logo --}}
        <div>
            <a href="/" style="font-weight: bold; text-decoration: none; color: #111827;">
                AI Assistant
            </a>
        </div>

        {{-- Navigation Links --}}
        <div>
            @auth
                {{-- Links for LOGGED IN users --}}
                <a href="{{ route('dashboard') }}" style="margin-right: 1rem; text-decoration: none; color: #374151;">Dashboard</a>
                <a href="/upload" style="margin-right: 1rem; text-decoration: none; color: #374151;">Upload File</a>
                <a href="/chat" style="margin-right: 1rem; text-decoration: none; color: #374151;">Chat</a>
            @else
                {{-- Links for GUESTS --}}
                <a href="{{ route('login') }}" style="margin-right: 1rem; text-decoration: none; color: #374151;">Log In</a>
                <a href="{{ route('register') }}" style="margin-right: 1rem; text-decoration: none; color: #374151;">Register</a>
            @endauth
        </div>

        {{-- Logout Button (only for logged-in users) --}}
        @auth
            <div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log Out</button>
                </form>
            </div>
        @endauth

    </div>
</nav>
