<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Assistant Builder - Turn Documents Into Smart AI Chatbots</title>
    <meta name="description" content="Transform your documents into intelligent AI assistants in minutes. Upload any file and create custom chatbots that understand your content perfectly.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(245, 48, 3, 0.3); }
            50% { box-shadow: 0 0 30px rgba(245, 48, 3, 0.5); }
        }
        
        .animate-fade-slide-up {
            animation: fade-slide-up 0.8s ease-out forwards;
        }
        
        .animate-fade-slide-in {
            animation: fade-slide-in 0.6s ease-out forwards;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #FDFDFC 0%, #f8f7f4 100%);
        }
        
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .demo-upload {
            border: 2px dashed #e5e5e5;
            transition: all 0.3s ease;
        }
        
        .demo-upload:hover {
            border-color: #f53003;
            background: rgba(245, 48, 3, 0.05);
        }
        
        .typing-animation::after {
            content: '|';
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
    </style>
</head>
<body class="gradient-bg dark:bg-gradient-to-br dark:from-[#0a0a0a] dark:to-[#1a1a1a] text-[#1b1b18] dark:text-white">
    <!-- Header -->
    <header class="w-full px-6 lg:px-8 py-4 animate-fade-slide-in">
        <nav class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-[#f53003] rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold">AI Assistant Builder</span>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="#" class="hidden sm:inline-block px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-[#f53003] transition-colors">
                    Pricing
                </a>
                <a href="#" class="hidden sm:inline-block px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-[#f53003] transition-colors">
                    Examples
                </a>
                <a href="#" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-md transition-all">
                    Log in
                </a>
                <a href="#" class="px-5 py-2 bg-[#f53003] text-white rounded-lg text-sm font-medium hover:bg-[#e02d00] transition-all hover:shadow-lg hover:-translate-y-0.5 active:scale-95">
                    Start Free
                </a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-12 lg:py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-slide-up">
                <div class="inline-flex items-center gap-2 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 px-3 py-1 rounded-full text-sm font-medium mb-6">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    Free to start • No credit card required
                </div>
                
                <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                    Turn Any Document Into Your 
                    <span class="text-[#f53003] relative">
                        Personal AI Expert
                        <svg class="absolute -bottom-2 left-0 w-full h-3 text-[#f53003] opacity-20" viewBox="0 0 200 12" fill="currentColor">
                            <path d="M2 8c4.5-1 9-2 13.5-2.5 4.5-.5 9-.5 13.5 0s9 1.5 13.5 2c4.5.5 9 .5 13.5 0s9-1.5 13.5-2c4.5-.5 9-.5 13.5 0s9 1.5 13.5 2c4.5.5 9 .5 13.5 0s9-1.5 13.5-2c4.5-.5 9-.5 13.5 0s9 1.5 13.5 2c4.5.5 9 .5 13.5 0"/>
                        </svg>
                    </span>
                </h1>
                
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    Upload any document and create an intelligent AI assistant in minutes. Get instant answers, automate support, and connect to Telegram—all powered by advanced RAG technology.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="#" class="inline-flex items-center justify-center px-8 py-4 bg-[#f53003] text-white rounded-xl font-semibold text-lg hover:bg-[#e02d00] transition-all hover:shadow-lg hover:-translate-y-1 active:scale-95 animate-pulse-glow">
                        Create Your AI Assistant
                        <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#demo" class="inline-flex items-center justify-center px-8 py-4 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold text-lg hover:border-[#f53003] hover:text-[#f53003] transition-all">
                        <svg class="mr-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Watch Demo
                    </a>
                </div>
                
                <!-- Social Proof -->
                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-blue-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-red-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full border-2 border-white dark:border-gray-800"></div>
                    </div>
                    <span>Join 2,500+ users building smarter AI assistants</span>
                </div>
            </div>
            
            <!-- Demo Preview -->
            <div class="animate-fade-slide-up lg:animate-float" style="animation-delay: 200ms;">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="ml-auto text-sm text-gray-400">AI Chat Assistant</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">U</div>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-tl-none px-4 py-3 max-w-xs">
                                <p class="text-sm">What are the key benefits of your pricing model?</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-[#f53003] rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div class="bg-[#f53003] text-white rounded-2xl rounded-tl-none px-4 py-3 max-w-sm">
                                <p class="text-sm">Based on your document, our tiered pricing offers three key benefits: flexible scaling, transparent costs, and premium support. The basic plan starts at $29/month...</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                            <span class="ml-2 typing-animation">AI is analyzing your document</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Demo Section -->
    <section id="demo" class="bg-gray-50 dark:bg-gray-900/50 py-16">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">See It In Action</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300">Upload a document and watch your AI assistant come to life</p>
            </div>
            
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8 card-hover">
                <div class="demo-upload rounded-xl p-12 text-center mb-8 cursor-pointer" onclick="this.style.borderColor='#f53003'; this.style.background='rgba(245,48,3,0.1)';">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">Drop your document here</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">PDF, DOCX, TXT files up to 10MB</p>
                    <button class="px-6 py-2 bg-[#f53003] text-white rounded-lg hover:bg-[#e02d00] transition-colors">
                        Choose File
                    </button>
                </div>
                
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    <span>Or try with our sample document: </span>
                    <button class="text-[#f53003] hover:underline">Company Handbook.pdf</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-16">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Everything You Need to Build Smart AI</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">From document upload to deployed chatbot in under 5 minutes. No technical skills required.</p>
        </div>
        
        <div class="grid lg:grid-cols-3 gap-8">
            <div class="card-hover bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 animate-fade-slide-in" style="animation-delay: 100ms;">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Smart Document Processing</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Advanced AI automatically extracts, indexes, and understands your content. Supports PDF, DOCX, TXT, and more formats.</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        OCR text extraction
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Semantic chunking
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Multi-language support
                    </li>
                </ul>
            </div>
            
            <div class="card-hover bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 animate-fade-slide-in" style="animation-delay: 200ms;">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Intelligent Conversations</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Context-aware responses that understand your documents deeply. Get accurate answers with source citations every time.</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        GPT-4 powered responses
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Source citations
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Context memory
                    </li>
                </ul>
            </div>
            
            <div class="card-hover bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 animate-fade-slide-in" style="animation-delay: 300ms;">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Seamless Integration</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Connect your AI assistant to Telegram, embed on websites, or use our API. Deploy anywhere your users are.</p>
                <ul class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Telegram bot integration
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Website embed widget
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        REST API access
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="bg-gray-50 dark:bg-gray-900/50 py-16">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">Loved by Teams Everywhere</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300">See how companies are transforming their customer support and internal knowledge</p>
            </div>
            
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <span class="text-yellow-400">★★★★★</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">"Reduced our support ticket volume by 60%. Our customers get instant answers from our help documentation 24/7."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">S</div>
                        <div>
                            <p class="font-semibold">Sarah Chen</p>
                            <p class="text-sm text-gray-500">Head of Customer Success</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <span class="text-yellow-400">★★★★★</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">"Setup took 5 minutes. Now our entire team manual is accessible through our Telegram bot. Game changer!"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">M</div>
                        <div>
                            <p class="font-semibold">Marcus Rodriguez</p>
                            <p class="text-sm text-gray-500">Operations Manager</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-900 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 card-hover">
                    <div class="flex items-center gap-1 mb-4">
                        <span class="text-yellow-400">★★★★★</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">"The AI understands our complex product docs perfectly. It's like having an expert available 24/7."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-red-600 rounded-full flex items-center justify-center text-white font-bold">A</div>
                        <div>
                            <p class="font-semibold">Alex Thompson</p>
                            <p class="text-sm text-gray-500">Technical Writer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="max-w-4xl mx-auto px-6 lg:px-8 py-16">
        <div class="bg-gradient-to-r from-[#f53003] to-[#ff4b20] rounded-3xl p-12 text-center text-white">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Ready to Build Your AI Assistant?</h2>
            <p class="text-xl mb-8 opacity-90">Join thousands of teams already using smart AI to transform their workflow</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
                <a href="#" class="inline-flex items-center px-8 py-4 bg-white text-[#f53003] rounded-xl font-semibold text-lg hover:bg-gray-100 transition-all hover:shadow-lg hover:-translate-y-1 active:scale-95">
                    Start Building for Free
                    <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="#" class="inline-flex items-center px-8 py-4 border-2 border-white/30 text-white rounded-xl font-semibold text-lg hover:border-white hover:bg-white/10 transition-all">
                    Schedule Demo
                </a>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 text-sm opacity-80">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    No credit card required
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Setup in under 5 minutes
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Cancel anytime
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="max-w-4xl mx-auto px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Frequently Asked Questions</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300">Everything you need to know about building AI assistants</p>
        </div>
        
        <div class="space-y-4">
            <details class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-100 dark:border-gray-800 cursor-pointer group">
                <summary class="flex items-center justify-between font-semibold text-lg list-none">
                    <span>How does the AI understand my documents?</span>
                    <svg class="w-5 h-5 transform group-open:rotate-45 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </summary>
                <p class="mt-4 text-gray-600 dark:text-gray-300">Our AI uses advanced Retrieval-Augmented Generation (RAG) technology to break down your documents into semantic chunks, create embeddings, and index them for lightning-fast, context-aware responses. It understands meaning, not just keywords.</p>
            </details>
            
            <details class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-100 dark:border-gray-800 cursor-pointer group">
                <summary class="flex items-center justify-between font-semibold text-lg list-none">
                    <span>What file formats do you support?</span>
                    <svg class="w-5 h-5 transform group-open:rotate-45 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </summary>
                <p class="mt-4 text-gray-600 dark:text-gray-300">We support PDF, DOCX, TXT, RTF, and many other text-based formats. Our OCR technology can even extract text from scanned documents and images. Files up to 10MB per upload.</p>
            </details>
            
            <details class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-100 dark:border-gray-800 cursor-pointer group">
                <summary class="flex items-center justify-between font-semibold text-lg list-none">
                    <span>How do I connect my AI to Telegram?</span>
                    <svg class="w-5 h-5 transform group-open:rotate-45 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </summary>
                <p class="mt-4 text-gray-600 dark:text-gray-300">Simply create a bot with @BotFather on Telegram, copy the bot token, and paste it into our integration settings. Your AI assistant will be live on Telegram in seconds with full conversation support.</p>
            </details>
            
            <details class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-100 dark:border-gray-800 cursor-pointer group">
                <summary class="flex items-center justify-between font-semibold text-lg list-none">
                    <span>Is my data secure and private?</span>
                    <svg class="w-5 h-5 transform group-open:rotate-45 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </summary>
                <p class="mt-4 text-gray-600 dark:text-gray-300">Absolutely. Your documents are encrypted at rest and in transit. We use enterprise-grade security, SOC 2 compliance, and never share your data with third parties. You can delete your data anytime.</p>
            </details>
            
            <details class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-100 dark:border-gray-800 cursor-pointer group">
                <summary class="flex items-center justify-between font-semibold text-lg list-none">
                    <span>Can I customize my AI assistant's responses?</span>
                    <svg class="w-5 h-5 transform group-open:rotate-45 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </summary>
                <p class="mt-4 text-gray-600 dark:text-gray-300">Yes! You can set custom instructions, adjust the AI's tone and personality, create custom prompts, and even train it with specific examples. Make it sound exactly like your brand.</p>
            </details>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-[#f53003] rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold">AI Assistant Builder</span>
                    </div>
                    <p class="text-gray-400 mb-4">Turn any document into an intelligent AI assistant in minutes.</p>
                    <div class="flex gap-4">
                        <a href="#" class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-[#f53003] transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-[#f53003] transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-[#f53003] transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994.021-.041.001-.09-.041-.106a13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.010c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.120.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418Z"/></svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API Documentation</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Integrations</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Tutorials</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Community</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 mt-8 text-center text-gray-400">
                <p>&copy; 2025 AI Assistant Builder. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Add some interactive behavior
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe elements for animation
            document.querySelectorAll('.card-hover').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease-out';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>