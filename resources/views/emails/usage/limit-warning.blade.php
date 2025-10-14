@extends('emails.layouts.base')

@section('content')
    <h1 class="email-title">Usage Limit Alert ⚠️</h1>
    
    <div class="email-content">
        <p>Hi {{ $user->name }},</p>
        
        <p>
            This is a friendly reminder that you're approaching the usage limits for your 
            <strong>{{ $user->plan?->name ?? 'Free' }}</strong> plan.
        </p>
    </div>
    
    <div class="info-box info-box-warning">
        <strong>📊 Your Current Usage:</strong><br><br>
        @if(isset($metrics))
            @foreach($metrics as $metric => $data)
                <strong>{{ $data['name'] }}:</strong> 
                {{ $data['used'] }} / {{ $data['limit'] }} 
                ({{ $data['percentage'] }}%)<br>
            @endforeach
        @else
            <strong>API Calls:</strong> {{ $usageData['used'] ?? 0 }} / {{ $usageData['limit'] ?? 0 }} ({{ $usagePercentage }}%)<br>
        @endif
    </div>
    
    @php
        $percentage = $usagePercentage ?? 80;
    @endphp
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value" style="{{ $percentage >= 90 ? 'color: #ef4444;' : ($percentage >= 75 ? 'color: #f59e0b;' : 'color: #F53003;') }}">
                {{ $percentage }}%
            </div>
            <div class="stat-label">Usage Level</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size: 20px;">
                @php
                    $remaining = isset($usageData) ? ($usageData['limit'] - $usageData['used']) : 0;
                @endphp
                {{ $remaining }}
            </div>
            <div class="stat-label">Remaining</div>
        </div>
    </div>
    
    <div class="email-content">
        <h3 style="margin-top: 30px; margin-bottom: 15px; color: #1b1b18;">💡 What Happens Next?</h3>
        
        @if($percentage >= 100)
            <p>
                You've reached your plan limit. To continue using our services without interruption, 
                please consider upgrading to a higher plan.
            </p>
        @elseif($percentage >= 90)
            <p>
                You're almost at your limit! To avoid any service interruption, we recommend 
                upgrading your plan now.
            </p>
        @else
            <p>
                You're approaching your plan limit. Consider upgrading to a higher tier to ensure 
                uninterrupted service and unlock additional features.
            </p>
        @endif
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/pricing" class="button">
            Upgrade Your Plan →
        </a>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <h3 style="margin-bottom: 15px; color: #1b1b18;">🚀 Why Upgrade?</h3>
        
        <ul style="list-style: none; padding: 0;">
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">✨</span>
                Higher usage limits
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">⚡</span>
                Faster response times
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">🎯</span>
                Advanced features and analytics
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">🛡️</span>
                Priority support
            </li>
        </ul>
    </div>
    
    <div class="email-content" style="text-align: center; margin-top: 20px;">
        <a href="{{ config('app.url') }}/dashboard" class="button button-secondary">
            View Usage Dashboard →
        </a>
    </div>
@endsection


