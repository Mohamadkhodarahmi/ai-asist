@extends('emails.layouts.base')

@section('content')
    @if($type === 'upgrade')
        <h1 class="email-title">Your Plan Has Been Upgraded! 🚀</h1>
    @elseif($type === 'downgrade')
        <h1 class="email-title">Your Plan Has Been Changed 📋</h1>
    @else
        <h1 class="email-title">Your Subscription Has Been Updated 🔄</h1>
    @endif
    
    <div class="email-content">
        <p>Hi {{ $user->name }},</p>
        
        @if($type === 'upgrade')
            <p>
                Great news! Your subscription has been successfully upgraded to <strong>{{ $newPlan }}</strong>.
                You now have access to all the enhanced features included in your new plan.
            </p>
        @elseif($type === 'downgrade')
            <p>
                Your subscription has been changed to <strong>{{ $newPlan }}</strong>. 
                This change will take effect immediately.
            </p>
        @else
            <p>
                Your subscription has been updated. Your new plan is <strong>{{ $newPlan }}</strong>.
            </p>
        @endif
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value" style="font-size: 20px; color: #706f6c;">{{ $oldPlan }}</div>
            <div class="stat-label">Previous Plan</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size: 20px; color: #10b981;">{{ $newPlan }}</div>
            <div class="stat-label">New Plan</div>
        </div>
    </div>
    
    @if($type === 'upgrade')
        <div class="info-box info-box-success">
            <strong>✨ What's New in Your Plan:</strong><br><br>
            @if($features)
                @foreach($features as $feature)
                    • {{ $feature }}<br>
                @endforeach
            @else
                • Enhanced API limits<br>
                • Advanced analytics<br>
                • Priority support<br>
                • Data export capabilities<br>
            @endif
        </div>
    @endif
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/dashboard" class="button">
            Explore Your New Features →
        </a>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        @if($type === 'upgrade')
            <p>
                <strong>🎉 Thank you for upgrading!</strong><br>
                We're committed to providing you with the best experience possible. 
                If you have any questions about your new features, we're here to help.
            </p>
        @else
            <p>
                <strong>Need Help?</strong><br>
                If you have any questions about your plan change, please don't hesitate to reach out to our support team.
            </p>
        @endif
    </div>
    
    <div class="email-content" style="text-align: center; margin-top: 20px;">
        <a href="{{ config('app.url') }}/pricing" class="button button-secondary">
            View All Plans →
        </a>
    </div>
@endsection


