@extends('emails.layouts.base')

@section('content')
    <h1 class="email-title">Your Export is Ready! 📦</h1>
    
    <div class="email-content">
        <p>Hi {{ $user->name }},</p>
        
        <p>
            Great news! We've finished processing your data export request. Your file is ready 
            for download and will be available for the next 7 days.
        </p>
    </div>
    
    <div class="info-box info-box-success">
        <strong>📋 Export Details:</strong><br><br>
        <strong>Format:</strong> {{ strtoupper($format) }}<br>
        <strong>Date Range:</strong> {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M j, Y') : 'All time' }} 
        - {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M j, Y') : 'Present' }}<br>
        <strong>Records:</strong> {{ $recordCount ?? 'Multiple' }}<br>
        <strong>File Size:</strong> {{ $fileSize ?? 'N/A' }}<br>
        <strong>Generated:</strong> {{ now()->format('F j, Y at g:i A') }}<br>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $downloadUrl }}" class="button">
            Download Your Export →
        </a>
    </div>
    
    <div class="info-box">
        <strong>🔒 Important:</strong><br>
        This download link will expire in <strong>7 days</strong> for security reasons. 
        Please download your export file as soon as possible.
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <h3 style="margin-bottom: 15px; color: #1b1b18;">📊 What's Included?</h3>
        
        <p>
            Your export contains:
        </p>
        
        <ul style="list-style: none; padding: 0;">
            @if($format === 'csv')
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">📄</span>
                    CSV file format (compatible with Excel, Google Sheets)
                </li>
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">💬</span>
                    All conversations and messages
                </li>
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">⏰</span>
                    Timestamps and metadata
                </li>
            @elseif($format === 'json')
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">💻</span>
                    Structured JSON format (developer-friendly)
                </li>
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">📊</span>
                    Complete data with full metadata
                </li>
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">🔗</span>
                    Nested relationships preserved
                </li>
            @else
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">📄</span>
                    Formatted PDF document (easy to read)
                </li>
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">🎨</span>
                    Beautiful formatting and layout
                </li>
                <li style="padding: 8px 0; padding-left: 24px; position: relative;">
                    <span style="position: absolute; left: 0;">📱</span>
                    Ready to share or archive
                </li>
            @endif
        </ul>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content">
        <h3 style="margin-bottom: 15px; color: #1b1b18;">🔄 Need Another Export?</h3>
        
        <p>
            You can create new exports anytime from your dashboard with different date ranges or formats.
        </p>
        
        <a href="{{ config('app.url') }}/export" class="button button-secondary">
            Create New Export →
        </a>
    </div>
    
    <div class="divider"></div>
    
    <div class="email-content" style="font-size: 14px;">
        <p>
            <strong>Having trouble downloading?</strong><br>
            Make sure you're logged in to your account, or contact our support team for assistance.
        </p>
    </div>
@endsection


