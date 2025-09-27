<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * This method returns YOUR custom registration page.
     */
    public function create()
    {
        return view('auth.register'); // <-- YOUR CUSTOM VIEW WITH ENHANCED UI
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data with custom error messages
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Please enter your full name.',
            'name.max' => 'Name must be less than 255 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Try signing in instead.',
            'password.required' => 'Please create a password.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        // Create a new user in the database.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Default to Free plan if available
        if ($freePlan = Plan::query()->where('slug', 'free')->first()) {
            $user->plan()->associate($freePlan);
            $user->save();
        }

        // Log the user in automatically
        Auth::login($user);

        // Redirect to the dashboard with success message and onboarding flag
        return redirect()->route('dashboard')
                        ->with('success', 'Welcome to AI Assistant Builder! Your account has been created successfully.')
                        ->with('show_onboarding', true); // Flag to show onboarding tour
    }
}