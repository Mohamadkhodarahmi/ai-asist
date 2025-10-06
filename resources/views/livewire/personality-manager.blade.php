<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">AI Personality Manager</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A] mt-2">Customize your AI assistant's tone, style, and behavior</p>
            </div>
            <button wire:click="toggleCreateForm" class="px-6 py-3 bg-[#F53003] text-white rounded-xl font-semibold hover:bg-[#c41e00] transition-all shadow-lg">
                {{ $showCreateForm ? 'Cancel' : '+ New Personality' }}
            </button>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-800/50 text-red-800 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Create/Edit Form --}}
        @if ($showCreateForm)
            <div class="mb-8 bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
                    {{ $editingId ? 'Edit Personality' : 'Create New Personality' }}
                </h3>
                
                <form wire:submit="{{ $editingId ? 'updatePersonality' : 'createPersonality' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                Personality Name *
                            </label>
                            <input type="text" wire:model="name" 
                                   class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#F53003] focus:border-transparent"
                                   placeholder="e.g., Professional Assistant, Friendly Helper">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Tone --}}
                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                Tone *
                            </label>
                            <select wire:model="tone" 
                                    class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#F53003] focus:border-transparent">
                                @foreach($toneOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('tone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Style --}}
                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                Style *
                            </label>
                            <select wire:model="style" 
                                    class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#F53003] focus:border-transparent">
                                @foreach($styleOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('style') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Greeting Message --}}
                        <div>
                            <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                                Custom Greeting (Optional)
                            </label>
                            <input type="text" wire:model="greeting_message" 
                                   class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#F53003] focus:border-transparent"
                                   placeholder="e.g., Hello! I'm here to help with your questions.">
                            @error('greeting_message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- System Prompt --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            Advanced: Custom System Prompt (Optional)
                        </label>
                        <textarea wire:model="system_prompt" rows="4"
                                  class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#F53003] focus:border-transparent"
                                  placeholder="Advanced users: Define custom instructions for the AI. Leave blank to use tone/style settings."></textarea>
                        @error('system_prompt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                            If provided, this will override the tone and style settings above.
                        </p>
                    </div>

                    {{-- Form Actions --}}
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="toggleCreateForm" 
                                class="px-4 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#F53003] text-white rounded-lg font-semibold hover:bg-[#c41e00] transition-all">
                            {{ $editingId ? 'Update Personality' : 'Create Personality' }}
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Personalities List --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($personalities as $personality)
                <div class="bg-white dark:bg-[#161615] rounded-xl p-6 border border-[#e3e3e0] dark:border-[#3E3E3A] {{ $personality->is_active ? 'ring-2 ring-[#F53003] border-[#F53003]' : '' }}">
                    {{-- Personality Info --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $personality->name }}</h3>
                            {{-- Active Badge --}}
                            @if($personality->is_active)
                                <span class="bg-[#F53003] text-white text-xs font-bold px-2 py-1 rounded-full">
                                    ACTIVE
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs rounded-full">
                                {{ ucfirst($personality->tone) }}
                            </span>
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs rounded-full">
                                {{ ucfirst($personality->style) }}
                            </span>
                        </div>
                        
                        @if($personality->greeting_message)
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] italic">
                                "{{ $personality->greeting_message }}"
                            </p>
                        @endif

                        @if($personality->system_prompt)
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                                <span class="font-medium">Custom Prompt:</span> {{ Str::limit($personality->system_prompt, 100) }}
                            </p>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-between items-center">
                        <div class="flex gap-2">
                            @if(!$personality->is_active)
                                <button wire:click="activatePersonality({{ $personality->id }})" 
                                        class="px-3 py-1 bg-[#F53003] text-white text-sm rounded-lg hover:bg-[#c41e00] transition-all">
                                    Activate
                                </button>
                            @endif
                            <button wire:click="editPersonality({{ $personality->id }})" 
                                    class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                Edit
                            </button>
                        </div>
                        
                        @if(!$personality->is_active)
                            <button wire:click="deletePersonality({{ $personality->id }})" 
                                    wire:confirm="Are you sure you want to delete this personality?"
                                    class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-sm rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-all">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white dark:bg-[#161615] rounded-xl border-2 border-dashed border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="w-16 h-16 bg-[#F53003]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">No AI Personalities Yet</h3>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">Create your first AI personality to customize how your assistant responds.</p>
                    <button wire:click="toggleCreateForm" 
                            class="px-6 py-2 bg-[#F53003] text-white rounded-lg font-semibold hover:bg-[#c41e00] transition-all">
                        Create Your First Personality
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Feature Info --}}
        <div class="mt-12 bg-gradient-to-r from-[#F53003]/5 to-[#FF4433]/5 rounded-xl p-6 border border-[#F53003]/20">
            <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-3">🎭 About AI Personalities</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                <div>
                    <h4 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Tone Options:</h4>
                    <ul class="space-y-1">
                        <li><strong>Professional:</strong> Business-appropriate responses</li>
                        <li><strong>Friendly:</strong> Warm and conversational</li>
                        <li><strong>Casual:</strong> Relaxed, like talking to a friend</li>
                        <li><strong>Formal:</strong> Academic and respectful</li>
                        <li><strong>Enthusiastic:</strong> Energetic and encouraging</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Style Options:</h4>
                    <ul class="space-y-1">
                        <li><strong>Helpful:</strong> Focus on actionable advice</li>
                        <li><strong>Concise:</strong> Brief and to the point</li>
                        <li><strong>Detailed:</strong> Comprehensive explanations</li>
                        <li><strong>Creative:</strong> Think outside the box</li>
                        <li><strong>Analytical:</strong> Data-driven insights</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
