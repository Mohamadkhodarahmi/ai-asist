@extends('emails.layouts.base')

@section('content')
    <h1 class="email-title">Welcome to withasisstant, {{ $user->name }}! 🎉</h1>
    
    <div class="email-content">
        <p>
            We're thrilled to have you join our community of innovators using AI to transform their workflows!
        </p>
        
        <p>
            Your account is now active and ready to help you create amazing AI-powered experiences.
        </p>
    </div>
    
    <div class="info-box info-box-success">
        <strong>✨ Your Account Details:</strong><br>
        Email: <strong>{{ $user->email }}</strong><br>
        Current Plan: <strong>{{ $user->plan?->name ?? 'Free' }}</strong><br>
        Member since: <strong>{{ $user->created_at->format('F j, Y') }}</strong>
    </div>
    
    <div class="email-content">
        <h3 style="margin-top: 30px; margin-bottom: 15px; color: #1b1b18;">🚀 Quick Start Guide</h3>
        
        <p><strong>1. Start Your First Conversation</strong><br>
        Jump into the chat and experience the power of AI assistance right away.</p>
        
        <p><strong>2. Upload Your Documents</strong><br>
        Add your knowledge base to make your AI assistant even smarter and more helpful.</p>
        
        <p><strong>3. Create Groups</strong><br>
        Collaborate with your team by creating groups for different projects or topics.</p>
        
        <p><strong>4. Explore Integrations</strong><br>
        Connect with Telegram and other platforms to extend your AI assistant's reach.</p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/chat" class="button">
            Start Your First Chat →
        </a>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <h3 style="margin-bottom: 15px; color: #1b1b18;">💎 Want More Features?</h3>
        
        <p>
            Upgrade to a paid plan to unlock advanced features like unlimited conversations, 
            data export, analytics, and priority support.
        </p>
        
        <a href="{{ config('app.url') }}/pricing" class="button button-secondary">
            View Pricing Plans →
        </a>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content" style="text-align: center;">
        <p>
            <strong>Need help getting started?</strong><br>
            Our <a href="{{ config('app.url') }}/docs" style="color: #F53003;">documentation</a> 
            is here to guide you every step of the way.
        </p>
    </div>
@endsection


