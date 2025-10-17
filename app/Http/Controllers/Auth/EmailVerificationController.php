<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification notice page.
     */
    public function show(Request $request)
    {
        return view('auth.verify-email');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Show the email correction form.
     */
    public function showEmailCorrection(Request $request)
    {
        return view('auth.correct-email');
    }

    /**
     * Update the user's email address.
     */
    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,'.$user->id,
            ],
            'password' => ['required', 'current_password'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Please enter your current password.',
            'password.current_password' => 'The password is incorrect.',
        ]);

        // Update the user's email
        $oldEmail = $user->email;
        $user->update([
            'email' => $request->email,
            'email_verified_at' => null, // Reset verification status
        ]);

        // Send new verification email
        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('status', 'email-updated')
            ->with('old_email', $oldEmail)
            ->with('new_email', $request->email);
    }

    /**
     * Show the account deletion confirmation.
     */
    public function showDeleteAccount(Request $request)
    {
        return view('auth.delete-account');
    }

    /**
     * Delete the user's account and redirect to registration.
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirm_delete' => ['required', 'accepted'],
        ], [
            'password.required' => 'Please enter your current password.',
            'password.current_password' => 'The password is incorrect.',
            'confirm_delete.required' => 'You must confirm account deletion.',
            'confirm_delete.accepted' => 'You must confirm account deletion.',
        ]);

        $user = $request->user();
        $userEmail = $user->email;

        // Log out the user
        Auth::logout();

        // Delete the user account
        $user->delete();

        // Redirect to registration with the old email pre-filled
        return redirect()->route('register')
            ->with('status', 'account-deleted')
            ->with('email', $userEmail)
            ->with('message', 'Your account has been deleted. You can now register with a different email address.');
    }

    /**
     * Handle email verification.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);

        // Check if user exists
        if (! $user) {
            return redirect()->route('login')->with('error', 'Verification link is invalid or expired. Please request a new verification email.');
        }

        // Verify the hash matches the user's email
        if (! hash_equals(sha1($user->email), $hash)) {
            return redirect()->route('login')->with('error', 'Invalid verification link. Please request a new verification email.');
        }

        // Check if user is already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('status', 'Email already verified!');
        }

        // Mark email as verified
        $user->markEmailAsVerified();

        // Log the user in
        auth()->login($user);

        return redirect()->route('dashboard')->with('status', 'Email verified successfully!');
    }
}
