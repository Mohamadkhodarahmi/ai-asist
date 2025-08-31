<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // We will create this next
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     * This method returns YOUR custom login page.
     */
    public function create()
    {
        return view('auth.login'); // <-- YOUR CUSTOM VIEW
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        // Attempt to authenticate the user.
        $request->authenticate();

        // Regenerate the session ID to prevent session fixation attacks.
        $request->session()->regenerate();

        // Redirect to the intended page or dashboard.
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session (logout).
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
