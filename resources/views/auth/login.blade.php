<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'AI Assistant Builder') }}</title>
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
        
        .gradient-bg {
            background: linear-gradient(135deg, #FDFDFC 0%, #f8f7f4 100%);
        }
        
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        }
        
        .form-input {
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 48, 3, 0.15);
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
        
        .error-message {
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
            <h1 class="text-3xl font-bold mb-2 text-[#1b1b18] dark:text-[#EDEDEC]">Welcome Back</h1>
            <p class="text-gray-600 dark:text-gray-300">Sign in to continue building your AI assistants</p>
        </div>

        <!-- Login form card -->
        <div class="bg-white/80 dark:bg-[#161615]/80 rounded-2xl shadow-2xl p-8 card animate-scale-in backdrop-blur-sm" style="animation-delay: 200ms;">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl error-message">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6" id="loginForm">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">Email Address</label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#f53003] focus:border-transparent transition-all form-input bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="Enter your email"
                        >
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">Password</label>
                        <div class="relative">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                class="w-full px-4 py-3 pr-12 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#f53003] focus:border-transparent transition-all form-input bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="Enter your password"
                            >
                            <button 
                                type="button" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                                onclick="togglePassword()"
                            >
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 text-[#f53003] bg-gray-100 border-gray-300 rounded focus:ring-[#f53003] focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        >
                        <label for="remember_me" class="text-sm text-gray-600 dark:text-gray-300">Remember me</label>
                    </div>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-[#f53003] hover:text-[#c41e00] transition-colors font-medium">
                            Forgot password?
                        </a>
                    @endif
                </div>
                
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-[#f53003] text-white rounded-xl hover:bg-[#c41e00] transition-all font-semibold shadow-lg btn-primary animate-pulse-border text-lg"
                    id="loginBtn"
                >
                    <span id="loginBtnText">Sign In</span>
                    <span id="loginBtnSpinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Signing in...
                    </span>
                </button>
                
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-300">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-[#f53003] hover:text-[#c41e00] transition-colors font-semibold hover:underline">
                            Create one now
                        </a>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- Social proof -->
        <div class="text-center mt-8 animate-fade-slide-in" style="animation-delay: 400ms;">
            <div class="flex items-center justify-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex -space-x-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-purple-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                    <div class="w-6 h-6 bg-gradient-to-br from-green-400 to-blue-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                    <div class="w-6 h-6 bg-gradient-to-br from-yellow-400 to-red-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                </div>
                <span>Trusted by 2,500+ users</span>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        // Form submission animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('loginBtnText');
            const btnSpinner = document.getElementById('loginBtnSpinner');
            
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            btn.disabled = true;
        });

        // Add floating animation to form inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach((input, index) => {
                input.style.animationDelay = `${300 + index * 100}ms`;
                input.classList.add('animate-fade-slide-in');
            });
        });
    </script>
</body>
</html>