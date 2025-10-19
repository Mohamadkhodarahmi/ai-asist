<div class="py-8 min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">🤖 Telegram Bot Builder</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Create and customize your AI-powered Telegram bot</p>
        </div>

        {{-- Loading Overlay --}}
        @if($isLoading || $isSaving)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-xl max-w-md w-full mx-4">
                    <div class="flex items-center justify-center mb-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $isSaving ? 'Saving...' : 'Loading...' }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $loadingMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Progress Steps --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $showTemplates ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            <span class="text-sm font-medium">1</span>
                        </div>
                        <span class="ml-2 text-sm font-medium {{ $showTemplates ? 'text-blue-600' : 'text-gray-600 dark:text-gray-400' }}">Template</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 dark:bg-gray-700"></div>
                    
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $showBasicSettings ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            <span class="text-sm font-medium">2</span>
                        </div>
                        <span class="ml-2 text-sm font-medium {{ $showBasicSettings ? 'text-blue-600' : 'text-gray-600 dark:text-gray-400' }}">Settings</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 dark:bg-gray-700"></div>
                    
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $showPersonalitySettings ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            <span class="text-sm font-medium">3</span>
                        </div>
                        <span class="ml-2 text-sm font-medium {{ $showPersonalitySettings ? 'text-blue-600' : 'text-gray-600 dark:text-gray-400' }}">Personality</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 dark:bg-gray-700"></div>
                    
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $showKeyboardSettings ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            <span class="text-sm font-medium">4</span>
                        </div>
                        <span class="ml-2 text-sm font-medium {{ $showKeyboardSettings ? 'text-blue-600' : 'text-gray-600 dark:text-gray-400' }}">Keyboard</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Template Selection --}}
        @if($showTemplates)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Choose a Template</h2>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">Start with a pre-built template or create from scratch</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($availableTemplates as $template)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-blue-500 transition-colors cursor-pointer group relative" 
                                 wire:click="selectTemplate({{ $template['id'] ?? 0 }})"
                                 wire:loading.class="opacity-50 pointer-events-none"
                                 wire:target="selectTemplate">
                                
                                {{-- Loading indicator for this template --}}
                                <div wire:loading wire:target="selectTemplate" class="absolute inset-0 bg-white bg-opacity-75 dark:bg-gray-800 dark:bg-opacity-75 rounded-lg flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                </div>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-2xl group-hover:scale-110 transition-transform">{{ $template['category_icon'] ?? '🤖' }}</span>
                                    @if($template['is_premium'])
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Premium</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Free</span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $template['name'] }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $template['description'] }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $template['usage_count'] }} uses</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $template['price'] == 0 ? 'Free' : '$' . $template['price'] }}</span>
                                </div>
                            </div>
                        @endforeach
                        
                        {{-- Create from Scratch --}}
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 hover:border-blue-500 transition-colors cursor-pointer flex flex-col items-center justify-center min-h-[200px]"
                             wire:click="toggleSection('BasicSettings')">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Create from Scratch</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 text-center">Build your bot from the ground up with full customization</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Basic Settings --}}
        @if($showBasicSettings)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Basic Settings</h2>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">Configure your bot's basic information and messages</p>
                        </div>
                        <button wire:click="toggleSection('Templates')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            ← Back to Templates
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bot Name</label>
                                <input type="text" wire:model="bot_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="My AI Assistant">
                                @error('bot_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Language</label>
                                <select wire:model="language" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    @foreach($this->getAvailableLanguages() as $code => $name)
                                        <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Welcome Message</label>
                                <textarea wire:model="welcome_message" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Hello! I'm your AI assistant. How can I help you today?"></textarea>
                                @error('welcome_message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Help Message</label>
                                <textarea wire:model="help_message" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="I can help you with questions about your documents. Just ask me anything!"></textarea>
                                @error('help_message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Error Message</label>
                                <textarea wire:model="error_message" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Sorry, I encountered an error. Please try again."></textarea>
                                @error('error_message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button wire:click="toggleSection('Templates')" 
                                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:target="toggleSection">
                            <span wire:loading.remove wire:target="toggleSection">← Back</span>
                            <span wire:loading wire:target="toggleSection" class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-600 mr-2"></div>
                                Loading...
                            </span>
                        </button>
                        <button wire:click="toggleSection('PersonalitySettings')" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:target="toggleSection">
                            <span wire:loading.remove wire:target="toggleSection">Next: Personality →</span>
                            <span wire:loading wire:target="toggleSection" class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                Loading...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Personality Settings --}}
        @if($showPersonalitySettings)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Personality Settings</h2>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">Define your bot's personality and communication style</p>
                        </div>
                        <button wire:click="toggleSection('BasicSettings')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            ← Back to Settings
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tone</label>
                                <select wire:model="personality_tone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    @foreach($this->getPersonalityTones() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Style</label>
                                <select wire:model="personality_style" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                    @foreach($this->getPersonalityStyles() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Personality Traits</label>
                                <div class="space-y-2">
                                    @foreach(['empathetic', 'analytical', 'creative', 'logical', 'patient', 'enthusiastic'] as $trait)
                                        <label class="flex items-center">
                                            <input type="checkbox" wire:model="personality_traits" value="{{ $trait }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 capitalize">{{ $trait }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button wire:click="toggleSection('BasicSettings')" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            ← Back
                        </button>
                        <button wire:click="toggleSection('KeyboardSettings')" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Next: Keyboard →
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Keyboard Settings --}}
        @if($showKeyboardSettings)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Keyboard Layout</h2>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">Create interactive keyboards and quick replies</p>
                        </div>
                        <button wire:click="toggleSection('PersonalitySettings')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            ← Back to Personality
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    {{-- Quick Replies --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Replies</h3>
                            <button wire:click="addQuickReply" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                    wire:loading.attr="disabled"
                                    wire:target="addQuickReply">
                                <span wire:loading.remove wire:target="addQuickReply">+ Add Reply</span>
                                <span wire:loading wire:target="addQuickReply" class="flex items-center">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                    Adding...
                                </span>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($quick_replies as $index => $reply)
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Button Text</label>
                                            <input type="text" wire:model="quick_replies.{{ $index }}.text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Quick reply text">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Response</label>
                                            <input type="text" wire:model="quick_replies.{{ $index }}.response" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Bot response">
                                        </div>
                                    </div>
                                    <button wire:click="removeQuickReply('{{ $reply['id'] ?? 'reply_' . $index }}')" 
                                            class="p-2 text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                            wire:loading.attr="disabled"
                                            wire:target="removeQuickReply('{{ $reply['id'] ?? 'reply_' . $index }}')">
                                        <svg wire:loading.remove wire:target="removeQuickReply('{{ $reply['id'] ?? 'reply_' . $index }}')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <div wire:loading wire:target="removeQuickReply('{{ $reply['id'] ?? 'reply_' . $index }}')" class="animate-spin rounded-full h-5 w-5 border-b-2 border-red-600"></div>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Commands --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Custom Commands</h3>
                            <button wire:click="addCommand" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                    wire:loading.attr="disabled"
                                    wire:target="addCommand">
                                <span wire:loading.remove wire:target="addCommand">+ Add Command</span>
                                <span wire:loading wire:target="addCommand" class="flex items-center">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                    Adding...
                                </span>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($commands as $index => $command)
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Command</label>
                                            <input type="text" wire:model="commands.{{ $index }}.command" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="/help">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                            <input type="text" wire:model="commands.{{ $index }}.description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Command description">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Response</label>
                                            <input type="text" wire:model="commands.{{ $index }}.response" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" placeholder="Command response">
                                        </div>
                                    </div>
                                    <button wire:click="removeCommand('{{ $command['id'] ?? 'command_' . $index }}')" 
                                            class="p-2 text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                            wire:loading.attr="disabled"
                                            wire:target="removeCommand('{{ $command['id'] ?? 'command_' . $index }}')">
                                        <svg wire:loading.remove wire:target="removeCommand('{{ $command['id'] ?? 'command_' . $index }}')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <div wire:loading wire:target="removeCommand('{{ $command['id'] ?? 'command_' . $index }}')" class="animate-spin rounded-full h-5 w-5 border-b-2 border-red-600"></div>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Keyboard Layout Management --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Keyboard Layout</h3>
                            <button wire:click="addKeyboardRow" 
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                    wire:loading.attr="disabled"
                                    wire:target="addKeyboardRow">
                                <span wire:loading.remove wire:target="addKeyboardRow">+ Add Row</span>
                                <span wire:loading wire:target="addKeyboardRow" class="flex items-center">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                    Adding...
                                </span>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($keyboard_layout as $rowIndex => $row)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="font-medium text-gray-900 dark:text-white">Row {{ $rowIndex + 1 }}</h4>
                                        <div class="flex gap-2">
                                            <button wire:click="addKeyboardButton('{{ $row['id'] }}')" 
                                                    class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                                    wire:loading.attr="disabled"
                                                    wire:target="addKeyboardButton('{{ $row['id'] }}')">
                                                <span wire:loading.remove wire:target="addKeyboardButton('{{ $row['id'] }}')">+ Button</span>
                                                <span wire:loading wire:target="addKeyboardButton('{{ $row['id'] }}')" class="flex items-center">
                                                    <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-white mr-1"></div>
                                                    Adding...
                                                </span>
                                            </button>
                                            <button wire:click="removeKeyboardRow('{{ $row['id'] }}')" 
                                                    class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                                    wire:loading.attr="disabled"
                                                    wire:target="removeKeyboardRow('{{ $row['id'] }}')">
                                                <span wire:loading.remove wire:target="removeKeyboardRow('{{ $row['id'] }}')">Remove Row</span>
                                                <span wire:loading wire:target="removeKeyboardRow('{{ $row['id'] }}')" class="flex items-center">
                                                    <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-white mr-1"></div>
                                                    Removing...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    @if(isset($row['buttons']) && count($row['buttons']) > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($row['buttons'] as $buttonIndex => $button)
                                                <div class="border border-gray-200 dark:border-gray-600 rounded p-3">
                                                    <div class="space-y-2">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Button Text</label>
                                                            <input type="text" 
                                                                   wire:model="keyboard_layout.{{ $rowIndex }}.buttons.{{ $buttonIndex }}.text" 
                                                                   class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" 
                                                                   placeholder="Button text">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Action</label>
                                                            <select wire:model="keyboard_layout.{{ $rowIndex }}.buttons.{{ $buttonIndex }}.action" 
                                                                    class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                                                <option value="text">Text</option>
                                                                <option value="url">URL</option>
                                                                <option value="callback">Callback</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Value</label>
                                                            <input type="text" 
                                                                   wire:model="keyboard_layout.{{ $rowIndex }}.buttons.{{ $buttonIndex }}.value" 
                                                                   class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" 
                                                                   placeholder="Action value">
                                                        </div>
                                                        <button wire:click="removeKeyboardButton('{{ $row['id'] }}', '{{ $button['id'] }}')" 
                                                                class="w-full px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                                                                wire:loading.attr="disabled"
                                                                wire:target="removeKeyboardButton('{{ $row['id'] }}', '{{ $button['id'] }}')">
                                                            <span wire:loading.remove wire:target="removeKeyboardButton('{{ $row['id'] }}', '{{ $button['id'] }}')">Remove</span>
                                                            <span wire:loading wire:target="removeKeyboardButton('{{ $row['id'] }}', '{{ $button['id'] }}')" class="flex items-center">
                                                                <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-white mr-1"></div>
                                                                Removing...
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">No buttons in this row. Click "Button" to add one.</p>
                                    @endif
                                </div>
                            @endforeach
                            
                            @if(count($keyboard_layout) === 0)
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm">No keyboard rows created yet.</p>
                                    <p class="text-xs">Click "Add Row" to create your first keyboard row.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button wire:click="toggleSection('PersonalitySettings')" 
                                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:target="toggleSection">
                            <span wire:loading.remove wire:target="toggleSection">← Back</span>
                            <span wire:loading wire:target="toggleSection" class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-600 mr-2"></div>
                                Loading...
                            </span>
                        </button>
                        <button wire:click="saveCustomization" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                wire:loading.attr="disabled"
                                wire:target="saveCustomization">
                            <span wire:loading.remove wire:target="saveCustomization">💾 Save Bot Configuration</span>
                            <span wire:loading wire:target="saveCustomization" class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>