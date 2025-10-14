@extends('emails.layouts.base')

@section('content')
    <h1 class="email-title">Payment Received Successfully ✅</h1>
    
    <div class="email-content">
        <p>Hi {{ $order->user->name }},</p>
        
        <p>
            Thank you for your payment! We've successfully processed your transaction, and your 
            subscription has been activated.
        </p>
    </div>
    
    <div class="info-box info-box-success">
        <strong>📋 Payment Details:</strong><br><br>
        <strong>Order ID:</strong> #{{ $order->id }}<br>
        <strong>Plan:</strong> {{ $order->plan->name }}<br>
        <strong>Amount:</strong> ${{ number_format($order->amount_usd, 2) }}<br>
        <strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'Cryptocurrency') }}<br>
        <strong>Status:</strong> <span style="color: #10b981;">{{ ucfirst($order->status) }}</span><br>
        <strong>Date:</strong> {{ $order->created_at->format('F j, Y at g:i A') }}<br>
        @if($order->transaction_id)
        <strong>Transaction ID:</strong> {{ $order->transaction_id }}<br>
        @endif
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">${{ number_format($order->amount_usd, 2) }}</div>
            <div class="stat-label">Total Paid</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size: 24px;">{{ $order->plan->name }}</div>
            <div class="stat-label">Your Plan</div>
        </div>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/dashboard" class="button">
            Go to Dashboard →
        </a>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <h3 style="margin-bottom: 15px; color: #1b1b18;">🎉 What's Next?</h3>
        
        <p>
            Your upgraded plan is now active! Here's what you can do:
        </p>
        
        <ul style="list-style: none; padding: 0;">
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">✨</span>
                Access all premium features
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">📊</span>
                Export your conversations and data
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">🔑</span>
                Generate API keys for integrations
            </li>
            <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                <span style="position: absolute; left: 0;">👥</span>
                Create unlimited groups and collaborations
            </li>
        </ul>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content" style="font-size: 14px;">
        <p>
            <strong>Questions about your payment?</strong><br>
            Contact our support team and we'll be happy to help.
        </p>
    </div>
@endsection


