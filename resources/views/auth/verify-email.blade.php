<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - {{ config('app.name', 'AI Assistant Builder') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}?v={{ time() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fade-slide-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fade-slide-in {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes scale-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        @keyframes pulse-border {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 48, 3, 0.4); }
            50% { box-shadow: 0 0 0 4px rgba(245, 48, 3, 0.1); }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .animate-fade-slide-up {
            animation: fade-slide-up 0.8s ease-out forwards;
        }
        
        .animate-fade-slide-in {
            animation: fade-slide-in 0.6s ease-out forwards;
        }
        
        .animate-scale-in {
            animation: scale-in 0.5s ease-out forwards;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animate-pulse-border {
            animation: pulse-border 2s infinite;
        }
        
        .animate-bounce {
            animation: bounce 2s infinite;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
        }
        
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            background-attachment: fixed;
        }
        
        .btn-primary {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 48, 3, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .success-message {
            animation: fade-slide-in 0.4s ease-out;
        }
        
        .card {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo-animation {
            transition: all 0.3s ease;
        }
        
        .logo-animation:hover {
            transform: scale(1.05) rotate(5deg);
        }
        
        .email-icon {
            animation: bounce 2s infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center gradient-bg relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-[#f53003]/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl animate-float" style="animation-delay: -1.5s;"></div>
    </div>
    
    <!-- Back to home button -->
    <div class="absolute top-6 left-6 animate-fade-slide-in">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-[#f53003] transition-all hover:bg-white/50 dark:hover:bg-black/20 rounded-lg backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Home
        </a>
    </div>

    <div class="w-full max-w-md p-8 relative z-10">
        <!-- Logo and brand -->
        <div class="text-center mb-8 animate-fade-slide-up">
            <div class="inline-flex items-center gap-2 mb-4">
                <div class="w-10 h-10 bg-[#f53003] rounded-xl flex items-center justify-center logo-animation">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ config('app.name', 'AI Assistant Builder') }}</span>
            </div>
            <h1 class="text-3xl font-bold mb-2 text-[#1b1b18] dark:text-[#EDEDEC]">Verify Your Email</h1>
            <p class="text-gray-600 dark:text-gray-300">We've sent you a verification link</p>
        </div>

        <!-- Verification notice card -->
        <div class="bg-white/80 dark:bg-[#161615]/80 rounded-2xl shadow-2xl p-8 card animate-scale-in backdrop-blur-sm" style="animation-delay: 200ms;">
            <!-- Success message for resend -->
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl success-message">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="font-medium">A new verification link has been sent to your email!</span>
                    </div>
                </div>
            @endif

            <!-- Email icon -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
                    <svg class="w-10 h-10 text-blue-600 dark:text-blue-400 email-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Instructions -->
            <div class="text-center space-y-4 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Check Your Email
                </h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    We've sent a verification link to <strong class="text-gray-900 dark:text-gray-100">{{ auth()->user()->email }}</strong>
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Click the link in the email to verify your account and start building your AI assistant.
                </p>
            </div>

            <!-- Resend form -->
            <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                @csrf
                
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-[#f53003] text-white rounded-xl hover:bg-[#c41e00] transition-all font-semibold shadow-lg btn-primary animate-pulse-border"
                    id="resendBtn"
                >
                    <span id="resendBtnText">Resend Verification Email</span>
                    <span id="resendBtnSpinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </form>
            
            <!-- Additional help -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                        Didn't receive the email?
                    </p>
                    <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                        <li>• Check your spam/junk folder</li>
                        <li>• Make sure you entered the correct email address</li>
                        <li>• Wait a few minutes and try resending</li>
                    </ul>
                </div>
            </div>
            
            <!-- Logout option -->
            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        Sign out and use a different account
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Help section -->
        <div class="text-center mt-8 animate-fade-slide-in" style="animation-delay: 400ms;">
            <div class="bg-white/60 dark:bg-gray-900/60 rounded-xl p-6 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50">
                <h3 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Need Help?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                    If you're having trouble verifying your email, we're here to help.
                </p>
                <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 text-sm text-[#f53003] hover:text-[#c41e00] transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Contact Support
                </a>
            </div>
        </div>
    </div>

    <script>
        // Form submission animation
        document.querySelector('form[action="{{ route('verification.send') }}"]').addEventListener('submit', function(e) {
            const btn = document.getElementById('resendBtn');
            const btnText = document.getElementById('resendBtnText');
            const btnSpinner = document.getElementById('resendBtnSpinner');
            
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            btn.disabled = true;
        });

        // Add floating animation to elements
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.animate-fade-slide-in');
            elements.forEach((element, index) => {
                element.style.animationDelay = `${300 + index * 100}ms`;
            });
        });
    </script>
</body>
</html>
