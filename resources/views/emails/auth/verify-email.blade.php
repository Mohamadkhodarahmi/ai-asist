@extends('emails.layouts.base')

@section('content')
    <h1 class="email-title">Verify Your Email Address 📧</h1>
    
    <div class="email-content">
        <p>Hello {{ $user->name }},</p>
        
        <p>
            Thank you for registering with withasisstant! To complete your account setup and 
            start using all our features, please verify your email address.
        </p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $verificationUrl }}" class="button">
            Verify Email Address
        </a>
    </div>
    
    <div class="info-box">
        <strong>🔒 Security Note:</strong><br>
        This link will expire in 60 minutes for your security. If you didn't create an account 
        with withasisstant, you can safely ignore this email.
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <p style="font-size: 14px; color: #706f6c;">
            <strong>Button not working?</strong><br>
            Copy and paste this link into your browser:<br>
            <a href="{{ $verificationUrl }}" style="color: #F53003; word-break: break-all;">{{ $verificationUrl }}</a>
        </p>
    </div>
@endsection


