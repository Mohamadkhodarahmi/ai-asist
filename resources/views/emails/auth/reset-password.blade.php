@extends('emails.layouts.base')

@section('content')
    <h1 class="email-title">Reset Your Password 🔐</h1>
    
    <div class="email-content">
        <p>Hi {{ $user->name }},</p>
        
        <p>
            We received a request to reset the password for your withasisstant account. 
            Click the button below to create a new password.
        </p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $resetUrl }}" class="button">
            Reset Password
        </a>
    </div>
    
    <div class="info-box info-box-warning">
        <strong>⏰ Time-Sensitive:</strong><br>
        This password reset link will expire in <strong>60 minutes</strong> for security reasons.
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <h3 style="margin-bottom: 15px; color: #1b1b18;">🛡️ Security Tips</h3>
        
        <ul style="list-style: none; padding: 0;">
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">✓</span>
                Use a strong, unique password
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">✓</span>
                Don't share your password with anyone
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">✓</span>
                Consider using a password manager
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">✓</span>
                Enable two-factor authentication (coming soon!)
            </li>
        </ul>
    </div>
    
    <div class="info-box info-box-danger">
        <strong>⚠️ Didn't Request This?</strong><br>
        If you didn't request a password reset, please ignore this email. Your password will 
        remain unchanged, and your account is secure. You may want to change your password 
        as a precaution if you suspect unauthorized access.
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content" style="font-size: 14px; color: #706f6c;">
        <p>
            <strong>Button not working?</strong><br>
            Copy and paste this link into your browser:<br>
            <a href="{{ $resetUrl }}" style="color: #F53003; word-break: break-all;">{{ $resetUrl }}</a>
        </p>
    </div>
@endsection


