@push('styles')
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    
    /* Custom scrollbar */
    #messages-container::-webkit-scrollbar {
        width: 6px;
    }
    
    #messages-container::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #messages-container::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }
    
    .dark #messages-container::-webkit-scrollbar-thumb {
        background: #4a5568;
    }
    
    /* Text Selection Colors */
    ::selection {
        background-color: #F53003;
        color: #ffffff;
    }
    
    ::-moz-selection {
        background-color: #F53003;
        color: #ffffff;
    }
    
    /* Make sure selected text is visible in all contexts */
    * ::selection {
        background-color: #F53003 !important;
        color: #ffffff !important;
    }
    
    * ::-moz-selection {
        background-color: #F53003 !important;
        color: #ffffff !important;
    }
    
    /* Alpine.js cloak */
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

<div class="py-8 bg-[#FDFDFC] dark:bg-[#0a0a0a]">

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 
                        border-l-4 border-green-500 text-green-800 dark:text-green-300 rounded-lg shadow-sm animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                {{ session('message') }}
                </div>
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 
                        border-l-4 border-red-500 text-red-800 dark:text-red-300 rounded-lg shadow-sm animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-[1fr_360px] gap-6">
            {{-- Main Chat Area --}}
            <div class="flex flex-col bg-white dark:bg-[#161615] rounded-2xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden h-[700px]">
                
                {{-- Chat Header --}}
                <div class="px-6 py-4 bg-gradient-to-r from-white to-gray-50 dark:from-[#1a1a1a] dark:to-[#161615] 
                            border-b border-[#e3e3e0] dark:border-[#3E3E3A] backdrop-blur-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-[#F53003] to-[#ff4422] rounded-xl 
                                        flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                {{ strtoupper(substr($group->name, 0, 1)) }}
                            </div>
                            <div>
                        <h2 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $group->name }}</h2>
                                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    {{ $group->members->count() }} members
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-2 hover:bg-gray-100 dark:hover:bg-[#3E3E3A] rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Messages Area --}}
                <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50 dark:bg-[#0a0a0a]">
                    
                    @forelse ($group->messages->sortBy('created_at') as $msg)
                        <div class="flex {{ $msg->is_ai_response ? 'justify-start' : ($msg->user_id == Auth::id() ? 'justify-end' : 'justify-start') }} 
                                    animate-fade-in" 
                             id="message-{{ $msg->id }}">
                            <div class="max-w-[75%] md:max-w-[60%]">
                                {{-- Sender Name --}}
                                    @if ($msg->is_ai_response)
                                    <div class="flex items-center gap-2 mb-2 ml-1">
                                        <div class="w-6 h-6 bg-gradient-to-br from-[#F53003] to-[#ff4422] rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-[#F53003] dark:text-[#FF4433]">AI Assistant</span>
                                    </div>
                                @elseif ($msg->user)
                                    <div class="flex items-center gap-2 mb-2 {{ $msg->user_id == Auth::id() ? 'justify-end mr-1' : 'ml-1' }}">
                                        @if ($msg->user_id != Auth::id())
                                            <div class="w-6 h-6 bg-gradient-to-br from-gray-400 to-gray-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                                {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A]">
                                            {{ $msg->user_id == Auth::id() ? 'You' : $msg->user->name }}
                                        </span>
                                        @if ($msg->user_id == Auth::id())
                                            <div class="w-6 h-6 bg-gradient-to-br from-[#F53003] to-[#ff4422] rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                                {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Message Bubble --}}
                                <div class="group relative">
                                    <div class="px-4 py-3 rounded-2xl shadow-sm transition-all duration-200
                                                {{ $msg->is_ai_response 
                                                    ? 'bg-gradient-to-br from-[#F53003]/5 to-[#ff4422]/10 dark:from-[#F53003]/10 dark:to-[#ff4422]/15 border border-[#F53003]/20' 
                                                    : ($msg->user_id == Auth::id() 
                                                        ? 'bg-gradient-to-br from-[#F53003] to-[#ff4422] text-white shadow-lg shadow-[#F53003]/30' 
                                                        : 'bg-white dark:bg-[#1a1a1a] border border-[#e3e3e0] dark:border-[#3E3E3A]')
                                                }}">
                                        <p class="text-sm leading-relaxed whitespace-pre-wrap break-words
                                                  {{ $msg->is_ai_response 
                                                      ? 'text-[#1b1b18] dark:text-[#EDEDEC]' 
                                                      : ($msg->user_id == Auth::id() 
                                                          ? 'text-white' 
                                                          : 'text-[#1b1b18] dark:text-[#EDEDEC]')
                                                  }}">{{ $msg->message }}</p>
                                    </div>
                                    
                                    {{-- Timestamp --}}
                                    <div class="flex items-center gap-2 mt-1 {{ $msg->user_id == Auth::id() ? 'justify-end' : 'justify-start' }} ml-1 mr-1">
                                        <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                            {{ $msg->created_at->format('h:i A') }}
                                        </span>
                                        @if ($msg->user_id == Auth::id() && !$msg->is_ai_response)
                                            <svg class="w-4 h-4 text-[#F53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                    @endif
                                    </div>
                                </div>
                                </div>
                            </div>
                        @empty
                        <div class="flex flex-col items-center justify-center h-full text-center py-12">
                            <div class="w-20 h-20 bg-gradient-to-br from-[#F53003]/10 to-[#ff4422]/20 rounded-2xl 
                                        flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-[#F53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">No messages yet</h3>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] max-w-md">
                                Start the conversation by sending a message or asking the AI assistant a question about your learning materials.
                            </p>
                            </div>
                        @endforelse
                    </div>

                {{-- AI Info --}}
                <div class="px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center gap-2 text-xs text-blue-700 dark:text-blue-300">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>AI responds only when tagged: <strong>@ai</strong>, <strong>@assistant</strong>, or <strong>hey ai</strong></span>
                    </div>
                </div>

                {{-- Input Area --}}
                <div class="p-4 bg-white dark:bg-[#161615] border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <form wire:submit.prevent="sendMessage" class="flex gap-3">
                        <div class="flex-1">
                            <textarea 
                                wire:model="message" 
                                rows="1" 
                                class="w-full px-4 py-3 rounded-xl border-2 border-[#e3e3e0] dark:border-[#3E3E3A] 
                                       bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] 
                                       focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:border-transparent
                                       resize-none transition-all duration-200 placeholder:text-[#706f6c]"
                                placeholder="Type your message... (Use @ai to get AI responses)"
                                x-data="{ resize: () => { $el.style.height = '44px'; $el.style.height = $el.scrollHeight + 'px' } }"
                                x-init="resize()"
                                @input="resize()"
                                @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendMessage(); $el.style.height = '44px'; }"
                            ></textarea>
                            @error('message') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>
                        <button 
                            type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-[#F53003] to-[#ff4422] text-white rounded-xl 
                                   font-semibold shadow-lg shadow-[#F53003]/30 hover:shadow-xl hover:shadow-[#F53003]/40
                                   hover:scale-105 active:scale-95 transition-all duration-200
                                   focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:ring-offset-2
                                   disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100
                                   flex items-center gap-2 self-end"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Send</span>
                            <span wire:loading>Sending...</span>
                            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                    <!-- <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Press Enter to send, Shift+Enter for new line
                    </p> -->
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">
                {{-- Members Card --}}
                <div class="bg-white dark:bg-[#161615] rounded-2xl shadow-lg p-5 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h3 class="font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Members ({{ $group->members->count() }})
                    </h3>
                    
                    {{-- Invite Form (Owner Only) --}}
                    @if($this->isOwner)
                    <form wire:submit.prevent="inviteMember" class="mb-4">
                            <input 
                                type="email" 
                                wire:model.defer="inviteEmail" 
                                placeholder="Enter user email..." 
                                class="w-full text-sm px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg 
                                       bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] 
                                       focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:border-transparent">
                            @error('inviteEmail') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span> 
                            @enderror
                            <button 
                                type="submit" 
                                class="mt-2 w-full py-2 px-3 bg-gradient-to-r from-[#F53003] to-[#ff4422] text-white rounded-lg 
                                       text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:scale-[1.02]">
                                + Invite Member
                            </button>
                    </form>
                    @else
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-[#1a1a1a] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
                            <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Only the group owner can invite members
                            </p>
                        </div>
                    @endif

                    {{-- Members List --}}
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach ($group->members as $member)
                            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-[#1a1a1a] transition-colors">
                                <div class="w-10 h-10 bg-gradient-to-br from-[#F53003] to-[#ff4422] rounded-xl 
                                            flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] truncate">{{ $member->name }}</p>
                                @if ($member->pivot && $member->pivot->role === 'owner')
                                        <span class="text-xs text-[#F53003] dark:text-[#FF4433]">Owner</span>
                                    @endif
                                </div>
                                @if ($member->pivot && $member->pivot->role !== 'owner')
                                    @if ($member->id === Auth::id())
                                        {{-- Leave Group Button for Current User --}}
                                        <div x-data="{ showConfirm: false }">
                                            <button 
                                                @click="showConfirm = true"
                                                class="px-3 py-1.5 text-xs font-medium text-orange-600 hover:text-white hover:bg-orange-600 
                                                       border border-orange-600 rounded-lg transition-all duration-200 hover:scale-105 hover:shadow-md"
                                                title="Leave group">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                                Leave
                                            </button>
                                            
                                            {{-- Confirmation Dialog --}}
                                            <div x-show="showConfirm" x-cloak 
                                                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                                 @click.self="showConfirm = false">
                                                <div class="bg-white dark:bg-[#161615] rounded-2xl p-6 max-w-sm mx-4 shadow-2xl border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                                    <div class="flex items-center gap-3 mb-4">
                                                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Leave Group</h3>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                                        Are you sure you want to leave this group? You won't be able to see messages or rejoin unless invited again.
                                                    </p>
                                                    <div class="flex gap-3">
                                                        <button @click="showConfirm = false" 
                                                                class="flex-1 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 
                                                                       bg-gray-100 dark:bg-[#1a1a1a] rounded-lg hover:bg-gray-200 dark:hover:bg-[#2a2a2a] 
                                                                       transition-colors">
                                                            Cancel
                                                        </button>
                                                        <button @click="$wire.removeMember({{ $member->id }}); showConfirm = false" 
                                                                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-orange-600 
                                                                       rounded-lg hover:bg-orange-700 transition-colors">
                                                            Leave Group
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($this->isOwner)
                                        {{-- Remove Member Button for Owner --}}
                                        <div x-data="{ showConfirm: false }">
                                            <button 
                                                @click="showConfirm = true"
                                                class="px-3 py-1.5 text-xs font-medium text-red-600 hover:text-white hover:bg-red-600 
                                                       border border-red-600 rounded-lg transition-all duration-200 hover:scale-105 hover:shadow-md"
                                                title="Remove member">
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Remove
                                            </button>
                                            
                                            {{-- Confirmation Dialog --}}
                                            <div x-show="showConfirm" x-cloak 
                                                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                                 @click.self="showConfirm = false">
                                                <div class="bg-white dark:bg-[#161615] rounded-2xl p-6 max-w-sm mx-4 shadow-2xl border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                                    <div class="flex items-center gap-3 mb-4">
                                                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Remove Member</h3>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                                        Are you sure you want to remove <strong>{{ $member->name }}</strong> from this group? They will lose access to all messages and files.
                                                    </p>
                                                    <div class="flex gap-3">
                                                        <button @click="showConfirm = false" 
                                                                class="flex-1 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 
                                                                       bg-gray-100 dark:bg-[#1a1a1a] rounded-lg hover:bg-gray-200 dark:hover:bg-[#2a2a2a] 
                                                                       transition-colors">
                                                            Cancel
                                                        </button>
                                                        <button @click="$wire.removeMember({{ $member->id }}); showConfirm = false" 
                                                                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 
                                                                       rounded-lg hover:bg-red-700 transition-colors">
                                                            Remove Member
                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Learning Materials Card --}}
                <div class="bg-white dark:bg-[#161615] rounded-2xl shadow-lg p-5 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h3 class="font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Knowledge Base
                        <span class="text-xs bg-[#F53003]/10 text-[#F53003] px-2 py-1 rounded-full font-medium">One File Only</span>
                    </h3>
                    
                    {{-- Upload Form (Owner Only) --}}
                    @if($this->isOwner)
                    <form wire:submit.prevent="uploadFileToGroup" class="mb-4">
                            <input 
                                type="file" 
                                wire:model="uploadFile" 
                                class="w-full text-xs text-gray-500 
                                       file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 
                                       file:text-sm file:font-semibold 
                                       file:bg-[#F53003]/10 file:text-[#F53003] 
                                       hover:file:bg-[#F53003]/20 file:cursor-pointer
                                       dark:file:bg-[#FF4433]/10 dark:file:text-[#FF4433]">
                            @error('uploadFile') 
                                <span class="text-xs text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span> 
                            @enderror
                            <button 
                                type="submit" 
                                class="mt-2 w-full py-2 px-3 bg-gradient-to-r from-[#F53003] to-[#ff4422] text-white rounded-lg 
                                       text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:scale-[1.02]"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    @if($group->files->count() > 0)
                                        Replace Knowledge Base
                                    @else
                                        Upload Knowledge Base
                                    @endif
                                </span>
                                <span wire:loading>Uploading...</span>
                            </button>
                    </form>
                    @else
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-[#1a1a1a] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
                            <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"/>
                                </svg>
                                Only the group owner can upload files
                            </p>
                        </div>
                    @endif

                    {{-- Files List --}}
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @forelse ($group->files as $file)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-[#1a1a1a] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                <div class="w-10 h-10 bg-[#F53003]/10 dark:bg-[#FF4433]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-[#F53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] truncate">
                                        {{ $file->knowledgeFile->original_name }}
                                    </p>
                                    <span class="inline-flex items-center gap-1 text-xs {{ $file->knowledgeFile->status === 'completed' ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            @if ($file->knowledgeFile->status === 'completed')
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            @else
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            @endif
                                        </svg>
                                        {{ ucfirst($file->knowledgeFile->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-[#706f6c] dark:text-[#A1A09A] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">No files uploaded yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
        // Auto-scroll to bottom when page loads
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('messages-container');
            if (container) {
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 100);
            }
            
            // Setup real-time messaging
            setupRealtimeMessaging();
        });
        
        // Auto-scroll when Livewire initializes
        document.addEventListener('livewire:initialized', () => {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
        
        // Auto-scroll after Livewire updates (only if Livewire is defined)
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('message.processed', (message, component) => {
                setTimeout(() => {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTo({
                            top: container.scrollHeight,
                            behavior: 'smooth'
                        });
                    }
                }, 100);
            });
        }
        
        // Fallback: Watch for DOM changes
        if (typeof MutationObserver !== 'undefined') {
            const container = document.getElementById('messages-container');
            if (container) {
                const observer = new MutationObserver(() => {
                    container.scrollTo({
                        top: container.scrollHeight,
                        behavior: 'smooth'
                    });
                });
                observer.observe(container, { childList: true, subtree: true });
            }
        }
        
        // Real-time messaging with Laravel Echo
        function setupRealtimeMessaging() {
            // Wait for Echo to be ready
            const checkEcho = setInterval(() => {
                if (typeof window.Echo !== 'undefined') {
                    clearInterval(checkEcho);
                    initializeEchoListener();
                }
            }, 100);
            
            // Timeout after 5 seconds
            setTimeout(() => clearInterval(checkEcho), 5000);
        }
        
        function initializeEchoListener() {
            const groupId = {{ $group->id }};
            const currentUserId = {{ auth()->id() }};
            
            console.log('🎧 Listening to group.' + groupId + ' channel...');
            
            const channel = window.Echo.private(`group.${groupId}`)
                .subscribed(() => {
                    console.log('✅ Successfully subscribed to group.' + groupId);
                })
                .listen('GroupMessageSent', (event) => {
                    console.log('📨 New message received (no prefix):', event);
                    appendMessageToUI(event, currentUserId);
                })
                .listen('.GroupMessageSent', (event) => {
                    console.log('📨 New message received (dot prefix):', event);
                    appendMessageToUI(event, currentUserId);
                })
                .error((error) => {
                    console.error('❌ Echo subscription error:', error);
                });
            
            // Listen to ALL events on this channel for debugging
            setTimeout(() => {
                if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                    const pusherChannel = window.Echo.connector.pusher.channels.channels['private-group.' + groupId];
                    if (pusherChannel) {
                        console.log('✅ Binding global listener to channel');
                        pusherChannel.bind_global((eventName, data) => {
                            console.log('🔔 ANY event received:', eventName, data);
                            if (eventName.includes('GroupMessageSent') || eventName.includes('TestEvent')) {
                                appendMessageToUI(data, currentUserId);
                            }
                        });
                    } else {
                        console.error('❌ Could not find pusher channel');
                    }
                } else {
                    console.error('❌ Echo or Pusher not available');
                }
            }, 1000);
        }
        
        function appendMessageToUI(event, currentUserId) {
            const container = document.getElementById('messages-container');
            if (!container) return;
            
            // Check if message already exists
            if (document.getElementById(`message-${event.id}`)) {
                return;
            }
            
            const isOwnMessage = event.user_id === currentUserId;
            const isAI = event.is_ai_response;
            const alignment = isAI ? 'justify-start' : (isOwnMessage ? 'justify-end' : 'justify-start');
            
            let senderHtml = '';
            if (isAI) {
                senderHtml = `
                    <div class="flex items-center gap-2 mb-2 ml-1">
                        <div class="w-6 h-6 bg-gradient-to-br from-[#F53003] to-[#ff4422] rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-[#F53003] dark:text-[#FF4433]">AI Assistant</span>
                    </div>`;
            } else if (event.user_name) {
                const initials = event.user_name.charAt(0).toUpperCase();
                const userClass = isOwnMessage ? 'justify-end mr-1' : 'ml-1';
                const labelText = isOwnMessage ? 'You' : event.user_name;
                
                senderHtml = `
                    <div class="flex items-center gap-2 mb-2 ${userClass}">
                        ${!isOwnMessage ? `
                            <div class="w-6 h-6 bg-gradient-to-br from-gray-400 to-gray-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                ${initials}
                            </div>` : ''}
                        <span class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A]">${labelText}</span>
                        ${isOwnMessage ? `
                            <div class="w-6 h-6 bg-gradient-to-br from-[#F53003] to-[#ff4422] rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                ${initials}
                            </div>` : ''}
                    </div>`;
            }
            
            const bubbleClasses = isAI 
                ? 'bg-gradient-to-br from-[#F53003]/5 to-[#ff4422]/10 dark:from-[#F53003]/10 dark:to-[#ff4422]/15 border border-[#F53003]/20'
                : (isOwnMessage 
                    ? 'bg-gradient-to-br from-[#F53003] to-[#ff4422] text-white shadow-lg shadow-[#F53003]/30'
                    : 'bg-white dark:bg-[#1a1a1a] border border-[#e3e3e0] dark:border-[#3E3E3A]');
            
            const textColor = isAI 
                ? 'text-[#1b1b18] dark:text-[#EDEDEC]'
                : (isOwnMessage ? 'text-white' : 'text-[#1b1b18] dark:text-[#EDEDEC]');
            
            const messageHtml = `
                <div class="flex ${alignment} animate-fade-in" id="message-${event.id}">
                    <div class="max-w-[75%] md:max-w-[60%]">
                        ${senderHtml}
                        <div class="group relative">
                            <div class="px-4 py-3 rounded-2xl shadow-sm transition-all duration-200 ${bubbleClasses}">
                                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words ${textColor}">${escapeHtml(event.message)}</p>
                            </div>
                            <div class="flex items-center gap-2 mt-1 ${isOwnMessage ? 'justify-end' : 'justify-start'} ml-1 mr-1">
                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">${event.created_at_human}</span>
                                ${isOwnMessage && !isAI ? `
                                    <svg class="w-4 h-4 text-[#F53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>` : ''}
                            </div>
                        </div>
                    </div>
                </div>`;
            
            container.insertAdjacentHTML('beforeend', messageHtml);
            
            // Auto-scroll to bottom
            setTimeout(() => {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            }, 100);
            
            // Play a subtle notification sound (optional)
            playNotificationSound();
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function playNotificationSound() {
            // Create a subtle beep using Web Audio API
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.1);
            } catch (e) {
                // Silently fail if audio not supported
            }
        }
    </script>
@endpush
</div>
