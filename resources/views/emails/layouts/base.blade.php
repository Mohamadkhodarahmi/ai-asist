<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $subject ?? 'Notification' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1b1b18;
            background-color: #f8f7f4;
            margin: 0;
            padding: 0;
        }
        
        .email-wrapper {
            width: 100%;
            background-color: #f8f7f4;
            padding: 40px 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #F53003 0%, #FF4433 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-logo {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .email-tagline {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .email-title {
            font-size: 24px;
            font-weight: bold;
            color: #1b1b18;
            margin-bottom: 20px;
        }
        
        .email-content {
            color: #706f6c;
            font-size: 16px;
            line-height: 1.8;
        }
        
        .email-content p {
            margin-bottom: 16px;
        }
        
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #F53003 0%, #FF4433 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        
        .button:hover {
            transform: translateY(-2px);
        }
        
        .button-secondary {
            background: #f3f3f0;
            color: #1b1b18 !important;
        }
        
        .info-box {
            background-color: #f3f3f0;
            border-left: 4px solid #F53003;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        
        .info-box-success {
            border-left-color: #10b981;
            background-color: #ecfdf5;
        }
        
        .info-box-warning {
            border-left-color: #f59e0b;
            background-color: #fffbeb;
        }
        
        .info-box-danger {
            border-left-color: #ef4444;
            background-color: #fef2f2;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin: 20px 0;
        }
        
        .stat-card {
            background-color: #f3f3f0;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #F53003;
            margin-bottom: 8px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #706f6c;
        }
        
        .divider {
            height: 1px;
            background-color: #e3e3e0;
            margin: 30px 0;
        }
        
        .email-footer {
            background-color: #f3f3f0;
            padding: 30px;
            text-align: center;
            color: #706f6c;
            font-size: 14px;
        }
        
        .footer-links {
            margin: 20px 0;
        }
        
        .footer-link {
            color: #F53003;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-link {
            display: inline-block;
            width: 32px;
            height: 32px;
            background-color: #ffffff;
            border-radius: 50%;
            margin: 0 5px;
            line-height: 32px;
            text-align: center;
            text-decoration: none;
        }
        
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }
            
            .email-body {
                padding: 30px 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .email-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <a href="{{ config('app.url') }}" class="email-logo">withasisstant</a>
                <div class="email-tagline">Your Intelligent AI Assistant Platform</div>
            </div>
            
            <!-- Body -->
            <div class="email-body">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <div class="footer-links">
                    <a href="{{ config('app.url') }}/dashboard" class="footer-link">Dashboard</a>
                    <a href="{{ config('app.url') }}/pricing" class="footer-link">Pricing</a>
                    <a href="{{ config('app.url') }}/docs" class="footer-link">Help Center</a>
                </div>
                
                <div class="divider" style="background-color: #e3e3e0; margin: 20px auto; width: 80%;"></div>
                
                <p style="margin: 10px 0;">
                    © {{ date('Y') }} withasisstant. All rights reserved.
                </p>
                
                <p style="margin: 10px 0; font-size: 12px; color: #A1A09A;">
                    You're receiving this email because you have an account with us.<br>
                    <a href="{{ config('app.url') }}/dashboard" style="color: #F53003;">Manage your preferences</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>


