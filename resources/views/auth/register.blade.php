<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ config('app.name', 'AI Assistant Builder') }}</title>
    
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
        
        @keyframes checkmark {
            0% { transform: scale(0) rotate(45deg); }
            50% { transform: scale(0.8) rotate(45deg); }
            100% { transform: scale(1) rotate(45deg); }
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
        
        .animate-checkmark {
            animation: checkmark 0.3s ease-out forwards;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
        }
        
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            background-attachment: fixed;
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
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .strength-weak { background: linear-gradient(to right, #ef4444 0%, #f87171 100%); width: 25%; }
        .strength-fair { background: linear-gradient(to right, #f59e0b 0%, #fbbf24 100%); width: 50%; }
        .strength-good { background: linear-gradient(to right, #10b981 0%, #34d399 100%); width: 75%; }
        .strength-strong { background: linear-gradient(to right, #059669 0%, #10b981 100%); width: 100%; }
        
        .form-step {
            opacity: 0;
            transform: translateX(30px);
            transition: all 0.4s ease;
        }
        
        .form-step.active {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg relative overflow-x-hidden py-8">
    <!-- Animated background elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-[#f53003]/5 rounded-full blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl animate-float" style="animation-delay: -1.5s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-500/3 rounded-full blur-3xl animate-float" style="animation-delay: -3s;"></div>
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

    <div class="w-full max-w-md mx-auto px-8 relative z-10 flex flex-col items-center justify-start"
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
            <h1 class="text-3xl font-bold mb-2 text-[#1b1b18] dark:text-[#EDEDEC]">Create Your Account</h1>
            <p class="text-gray-600 dark:text-gray-300">Join thousands building smarter AI assistants</p>
        </div>

        <!-- Register form card -->
        <div class="bg-white/80 dark:bg-[#161615]/80 rounded-2xl shadow-2xl p-8 card animate-scale-in backdrop-blur-sm" style="animation-delay: 200ms;">
            <!-- Success message for registration -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl success-message">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 animate-checkmark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

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

            <form method="POST" action="{{ route('register') }}" class="space-y-6" id="registerForm">
                @csrf
                
                <div class="space-y-4">
                    <div class="form-step active">
                        <label for="name" class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">Full Name</label>
                        <input 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus 
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#f53003] focus:border-transparent transition-all form-input bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="Enter your full name"
                        >
                    </div>
                    
                    <div class="form-step">
                        <label for="email" class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">Email Address</label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $email ?? '') }}" 
                            required 
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#f53003] focus:border-transparent transition-all form-input bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            placeholder="Enter your email address"
                        >
                    </div>
                    
                    <div class="form-step">
                        <label for="password" class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">Password</label>
                        <div class="relative">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                class="w-full px-4 py-3 pr-12 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#f53003] focus:border-transparent transition-all form-input bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="Create a strong password"
                                oninput="checkPasswordStrength(this.value)"
                            >
                            <button 
                                type="button" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                                onclick="togglePassword('password')"
                            >
                                <svg id="passwordEyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <!-- Password strength indicator -->
                        <div class="mt-2">
                            <div class="flex gap-1 mb-1">
                                <div class="password-strength bg-gray-200 dark:bg-gray-700" id="strengthBar"></div>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            </div>
                            <p id="strengthText" class="text-xs text-gray-500 dark:text-gray-400">Enter a password to see strength</p>
                        </div>
                    </div>
                    
                    <div class="form-step">
                        <label for="password_confirmation" class="block text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">Confirm Password</label>
                        <div class="relative">
                            <input 
                                id="password_confirmation" 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                class="w-full px-4 py-3 pr-12 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#f53003] focus:border-transparent transition-all form-input bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                placeholder="Confirm your password"
                                oninput="checkPasswordMatch()"
                            >
                            <button 
                                type="button" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                                onclick="togglePassword('password_confirmation')"
                            >
                                <svg id="confirmEyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <div id="passwordMatchIndicator" class="mt-2 hidden">
                            <p id="matchText" class="text-xs"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Terms and conditions -->
                <div class="form-step">
                    <div class="flex items-start gap-3">
                        <input 
                            id="terms" 
                            type="checkbox" 
                            required
                            class="w-4 h-4 text-[#f53003] bg-gray-100 border-gray-300 rounded focus:ring-[#f53003] focus:ring-2 dark:bg-gray-700 dark:border-gray-600 mt-1"
                        >
                        <label for="terms" class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                            I agree to the 
                            <a href="#" class="text-[#f53003] hover:text-[#c41e00] transition-colors font-medium hover:underline">Terms of Service</a> 
                            and 
                            <a href="#" class="text-[#f53003] hover:text-[#c41e00] transition-colors font-medium hover:underline">Privacy Policy</a>
                        </label>
                    </div>
                </div>
                
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-[#f53003] text-white rounded-xl hover:bg-[#c41e00] transition-all font-semibold shadow-lg btn-primary animate-pulse-border text-lg"
                    id="registerBtn"
                >
                    <span id="registerBtnText">Create Account</span>
                    <span id="registerBtnSpinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating account...
                    </span>
                </button>
                
                <div class="text-center">
                    <p class="text-gray-600 dark:text-gray-300">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-[#f53003] hover:text-[#c41e00] transition-colors font-semibold hover:underline">
                            Sign in instead
                        </a>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- Benefits preview -->
        <div class="mt-8 animate-fade-slide-in" style="animation-delay: 600ms;">
            <div class="bg-white/60 dark:bg-gray-900/60 rounded-xl p-6 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50">
                <h3 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">What you get:</h3>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Free forever plan with unlimited documents
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Instant Telegram bot integration
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Advanced AI with GPT-4 intelligence
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Social proof -->
        <div class="text-center mt-6 mb-16 animate-fade-slide-in" style="animation-delay: 800ms;">
            <div class="flex items-center justify-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex -space-x-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-purple-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                    <div class="w-6 h-6 bg-gradient-to-br from-green-400 to-blue-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                    <div class="w-6 h-6 bg-gradient-to-br from-yellow-400 to-red-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                </div>
                <span>Join 2,500+ happy users</span>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIconId = inputId === 'password' ? 'passwordEyeIcon' : 'confirmEyeIcon';
            const eyeIcon = document.getElementById(eyeIconId);
            
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

        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length === 0) {
                strengthBar.className = 'password-strength bg-gray-200 dark:bg-gray-700';
                strengthText.textContent = 'Enter a password to see strength';
                strengthText.className = 'text-xs text-gray-500 dark:text-gray-400';
                return;
            }
            
            let strength = 0;
            let feedback = [];
            
            // Length check
            if (password.length >= 8) strength += 1;
            else feedback.push('at least 8 characters');
            
            // Uppercase check
            if (/[A-Z]/.test(password)) strength += 1;
            else feedback.push('uppercase letter');
            
            // Lowercase check
            if (/[a-z]/.test(password)) strength += 1;
            else feedback.push('lowercase letter');
            
            // Number check
            if (/\d/.test(password)) strength += 1;
            else feedback.push('number');
            
            // Special character check
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
            else feedback.push('special character');
            
            // Update strength indicator
            if (strength <= 2) {
                strengthBar.className = 'password-strength strength-weak';
                strengthText.textContent = 'Weak password';
                strengthText.className = 'text-xs text-red-500';
            } else if (strength === 3) {
                strengthBar.className = 'password-strength strength-fair';
                strengthText.textContent = 'Fair password';
                strengthText.className = 'text-xs text-yellow-500';
            } else if (strength === 4) {
                strengthBar.className = 'password-strength strength-good';
                strengthText.textContent = 'Good password';
                strengthText.className = 'text-xs text-blue-500';
            } else {
                strengthBar.className = 'password-strength strength-strong';
                strengthText.textContent = 'Strong password';
                strengthText.className = 'text-xs text-green-500';
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const indicator = document.getElementById('passwordMatchIndicator');
            const matchText = document.getElementById('matchText');
            
            if (confirmPassword.length === 0) {
                indicator.classList.add('hidden');
                return;
            }
            
            indicator.classList.remove('hidden');
            
            if (password === confirmPassword) {
                matchText.textContent = 'Passwords match ✓';
                matchText.className = 'text-xs text-green-500';
            } else {
                matchText.textContent = 'Passwords do not match';
                matchText.className = 'text-xs text-red-500';
            }
        }

        // Form submission animation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('registerBtn');
            const btnText = document.getElementById('registerBtnText');
            const btnSpinner = document.getElementById('registerBtnSpinner');
            
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            btn.disabled = true;
        });

        // Progressive form reveal animation
        document.addEventListener('DOMContentLoaded', function() {
            const formSteps = document.querySelectorAll('.form-step');
            
            formSteps.forEach((step, index) => {
                setTimeout(() => {
                    step.classList.add('active');
                }, index * 150);
            });
        });
    </script>
</body>
</html>