<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account - {{ config('app.name', 'AI Assistant Builder') }}</title>
    
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
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
        }
        
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            background-attachment: fixed;
        }
        
        .btn-danger {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-danger::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-danger:hover::before {
            left: 100%;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
        }
        
        .btn-danger:active {
            transform: translateY(0);
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
        
        .warning-icon {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center gradient-bg relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-red-500/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-orange-500/5 rounded-full blur-3xl animate-float" style="animation-delay: -1.5s;"></div>
    </div>
    
    <!-- Back to email correction button -->
    <div class="absolute top-6 left-6 animate-fade-slide-in">
        <a href="{{ route('verification.correct-email') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-[#f53003] transition-all hover:bg-white/50 dark:hover:bg-black/20 rounded-lg backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Email Correction
        </a>
    </div>

    <div class="w-full max-w-md p-8 relative z-10">
        <!-- Logo and brand -->
        <div class="text-center mb-8 animate-fade-slide-up">
            <div class="inline-flex items-center gap-2 mb-4">
                <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center logo-animation">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ config('app.name', 'AI Assistant Builder') }}</span>
            </div>
            <h1 class="text-3xl font-bold mb-2 text-[#1b1b18] dark:text-[#EDEDEC]">Delete Account</h1>
            <p class="text-gray-600 dark:text-gray-300">This action cannot be undone</p>
        </div>

        <!-- Account deletion form -->
        <div class="bg-white/80 dark:bg-[#161615]/80 rounded-2xl shadow-2xl p-8 card animate-scale-in backdrop-blur-sm" style="animation-delay: 200ms;">
            <!-- Warning icon -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <svg class="w-10 h-10 text-red-600 dark:text-red-400 warning-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    ⚠️ Warning: Account Deletion
                </h2>
                <p class="text-gray-600 dark:text-gray-300">
                    You are about to permanently delete your account and all associated data.
                </p>
            </div>

            <!-- What will be deleted -->
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-red-800 dark:text-red-200 mb-2">What will be deleted:</h3>
                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                    <li>• Your account and profile information</li>
                    <li>• All uploaded documents and files</li>
                    <li>• Chat history and conversations</li>
                    <li>• Telegram bot configurations</li>
                    <li>• Analytics and usage data</li>
                    <li>• All settings and preferences</li>
                </ul>
            </div>

            <!-- Delete account form -->
            <form method="POST" action="{{ route('verification.delete-account') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Current Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-all"
                        placeholder="Enter your current password"
                        required
                        autofocus
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-start space-x-3">
                        <input 
                            type="checkbox" 
                            name="confirm_delete" 
                            value="1"
                            class="mt-1 rounded border-gray-300 text-red-600 focus:ring-red-500"
                            required
                        >
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            I understand that this action is <strong>permanent and irreversible</strong>. I want to delete my account and all associated data.
                        </span>
                    </label>
                    @error('confirm_delete')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-semibold shadow-lg btn-danger"
                    id="deleteBtn"
                >
                    <span id="deleteBtnText">Delete My Account</span>
                    <span id="deleteBtnSpinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Deleting...
                    </span>
                </button>
            </form>
            
            <!-- Alternative options -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Changed your mind?
                    </p>
                    
                    <a href="{{ route('verification.correct-email') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Go Back to Email Correction
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Help section -->
        <div class="text-center mt-8 animate-fade-slide-in" style="animation-delay: 400ms;">
            <div class="bg-white/60 dark:bg-gray-900/60 rounded-xl p-6 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50">
                <h3 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Need Help?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                    If you're having trouble with your account, we're here to help.
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
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = document.getElementById('deleteBtn');
            const btnText = document.getElementById('deleteBtnText');
            const btnSpinner = document.getElementById('deleteBtnSpinner');
            
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


