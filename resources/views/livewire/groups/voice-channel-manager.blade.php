@push('styles')
<style>
    .voice-indicator {
        @apply w-3 h-3 rounded-full transition-all duration-300;
    }
    
    .voice-indicator.speaking {
        @apply bg-green-500 animate-pulse;
    }
    
    .voice-indicator.muted {
        @apply bg-red-500;
    }
    
    .voice-indicator.normal {
        @apply bg-gray-400;
    }
    
    .voice-indicator.deafened {
        @apply bg-gray-600;
    }
    
    .participant-avatar {
        @apply w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-md transition-all duration-200;
    }
    
    .participant-avatar.speaking {
        @apply ring-4 ring-green-500 ring-opacity-75 animate-pulse;
        box-shadow: 0 0 20px rgba(34, 197, 94, 0.6);
    }
    
    .participant-avatar.muted {
        @apply ring-2 ring-red-500 ring-opacity-50;
    }
    
    .participant-speaking {
        @apply border-2 border-green-500 bg-green-50 dark:bg-green-900/20;
        box-shadow: 0 0 15px rgba(34, 197, 94, 0.4);
        animation: speaking-pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes speaking-pulse {
        0%, 100% {
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.4);
        }
        50% {
            box-shadow: 0 0 25px rgba(34, 197, 94, 0.8);
        }
    }
</style>
@endpush

<div class="bg-white dark:bg-[#161615] rounded-2xl shadow-lg p-5 border border-[#e3e3e0] dark:border-[#3E3E3A]">
    <h3 class="font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
        </svg>
        Voice Channels
        @if($currentVoiceChannel)
            <span class="text-xs bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded-full font-medium">Connected</span>
        @endif
    </h3>
    
    {{-- Current Voice Session Controls --}}
    @if($currentVoiceChannel)
        <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 
                    border border-green-200 dark:border-green-800 rounded-xl">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center {{ $isSpeaking ? 'ring-4 ring-green-400 ring-opacity-75 animate-pulse' : '' }}" 
                         style="{{ $isSpeaking ? 'box-shadow: 0 0 20px rgba(34, 197, 94, 0.6);' : '' }}">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $currentVoiceChannel->name }}</h4>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] flex items-center gap-1">
                            @if($isSpeaking)
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                Speaking • {{ $currentVoiceChannel->participants_count }} participants
                            @else
                                {{ $currentVoiceChannel->participants_count }} participants
                            @endif
                        </p>
                    </div>
                </div>
                <button wire:click="leaveVoiceChannel" 
                        class="px-3 py-1.5 text-xs font-medium text-red-600 hover:text-white hover:bg-red-600 
                               border border-red-600 rounded-lg transition-all duration-200 hover:scale-105">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Leave
                </button>
            </div>
            
            {{-- Voice Controls --}}
            <div class="flex items-center gap-3">
                <button wire:click="toggleMute" 
                        class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:scale-105
                               {{ $isMuted ? 'bg-red-500 text-white' : 'bg-gray-100 dark:bg-[#1a1a1a] text-[#1b1b18] dark:text-[#EDEDEC]' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($isMuted)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        @endif
                    </svg>
                    {{ $isMuted ? 'Unmute' : 'Mute' }}
                </button>
                
                <button wire:click="toggleDeafen" 
                        class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:scale-105
                               {{ $isDeafened ? 'bg-gray-600 text-white' : 'bg-gray-100 dark:bg-[#1a1a1a] text-[#1b1b18] dark:text-[#EDEDEC]' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($isDeafened)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        @endif
                    </svg>
                    {{ $isDeafened ? 'Undeafen' : 'Deafen' }}
                </button>
                
                <button onmousedown="handlePushToTalk(event, true)" onmouseup="handlePushToTalk(event, false)" onmouseleave="handlePushToTalk(event, false)"
                        data-voice-channel-id="{{ $currentVoiceChannel->id }}"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:scale-105
                               {{ $isSpeaking ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-[#1a1a1a] text-[#1b1b18] dark:text-[#EDEDEC]' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2"/>
                    </svg>
                    Push to Talk
                </button>
                
                <button id="testBeepBtn" 
                        data-voice-channel-id="{{ $currentVoiceChannel->id }}"
                        onclick="sendTestBeep()"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:scale-105
                               bg-purple-500 text-white hover:bg-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                    🔊 Test Beep
                </button>
                
                <button id="startVoiceBtn" 
                        data-voice-channel-id="{{ $currentVoiceChannel->id }}"
                        onclick="startVoiceStreamManually()"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 hover:scale-105
                               bg-green-500 text-white hover:bg-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    🎤 Start Voice
                </button>
            </div>
        </div>
    @endif
    
    {{-- Create New Voice Channel (Owner Only) --}}
    @if($isOwner)
        <div class="mb-4 p-4 bg-gray-50 dark:bg-[#1a1a1a] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
            <h4 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#F53003]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Voice Channel
            </h4>
            
            <form wire:submit.prevent="createVoiceChannel" class="space-y-3">
                <input wire:model="newChannelName" 
                       type="text" 
                       placeholder="Channel name..." 
                       class="w-full text-sm px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg 
                              bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] 
                              focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:border-transparent">
                @error('newChannelName') 
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> 
                @enderror
                
                <textarea wire:model="newChannelDescription" 
                          placeholder="Channel description (optional)..." 
                          rows="2"
                          class="w-full text-sm px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg 
                                 bg-white dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] 
                                 focus:outline-none focus:ring-2 focus:ring-[#F53003] focus:border-transparent resize-none"></textarea>
                @error('newChannelDescription') 
                    <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> 
                @enderror
                
                <button type="submit" 
                        class="w-full py-2 px-3 bg-gradient-to-r from-[#F53003] to-[#ff4422] text-white rounded-lg 
                               text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:scale-[1.02]">
                    Create Channel
                </button>
            </form>
        </div>
    @endif
    
    {{-- Voice Channels List --}}
    <div class="space-y-3 max-h-64 overflow-y-auto">
        @forelse ($voiceChannels as $channel)
            <div class="p-3 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] 
                        {{ $channel->id === $currentVoiceChannel?->id ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-white dark:bg-[#1a1a1a]' }}">
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-[#F53003] to-[#ff4422] rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h5 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $channel->name }}</h5>
                            @if($channel->description)
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $channel->description }}</p>
                            @endif
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $channel->participants_count }}/{{ $channel->max_participants }} participants</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @if($channel->id === $currentVoiceChannel?->id)
                            <span class="text-xs bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded-full font-medium">
                                Connected
                            </span>
                        @elseif($channel->hasUser(auth()->id()))
                            <span class="text-xs bg-blue-500/10 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-full font-medium">
                                In Channel
                            </span>
                        @else
                            @if($channel->isFull())
                                <span class="text-xs bg-red-500/10 text-red-600 dark:text-red-400 px-2 py-1 rounded-full font-medium">
                                    Full
                                </span>
                            @else
                                <button wire:click="joinVoiceChannel({{ $channel->id }})" 
                                        onclick="console.log('Join button clicked for channel {{ $channel->id }}')"
                                        class="px-3 py-1.5 text-xs font-medium text-[#F53003] hover:text-white hover:bg-[#F53003] 
                                               border border-[#F53003] rounded-lg transition-all duration-200 hover:scale-105">
                                    Join
                                </button>
                            @endif
                        @endif
                        
                        @if($isOwner)
                            <button wire:click="deleteVoiceChannel({{ $channel->id }})" 
                                    class="p-1.5 text-red-600 hover:text-white hover:bg-red-600 rounded-lg transition-all duration-200"
                                    title="Delete channel">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                
                {{-- Participants --}}
                @if($channel->activeSessions->count() > 0)
                    <div class="mt-3 flex items-center gap-2 flex-wrap participant-avatars-container">
                        @foreach($channel->activeSessions as $session)
                            <div class="flex items-center gap-2 px-2 py-1 rounded-lg transition-all duration-300 {{ $session->is_speaking ? 'participant-speaking' : 'bg-gray-100 dark:bg-[#2a2a2a]' }}" data-user-id="{{ $session->user_id }}">
                                <div class="participant-avatar {{ $session->is_speaking ? 'speaking' : ($session->is_muted ? 'muted' : '') }} 
                                            bg-gradient-to-br from-[#F53003] to-[#ff4422]">
                                    {{ strtoupper(substr($session->user->name, 0, 1)) }}
                                </div>
                                <span class="text-xs font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $session->user->name }}</span>
                                <div class="voice-indicator {{ $session->is_speaking ? 'speaking' : ($session->is_muted ? 'muted' : ($session->is_deafened ? 'deafened' : 'normal')) }}"></div>
                                @if($session->is_speaking)
                                    <div class="flex items-center gap-1">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">Speaking</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-[#706f6c] dark:text-[#A1A09A] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                </svg>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">No voice channels yet</p>
                @if($isOwner)
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">Create one to start voice conversations!</p>
                @endif
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    // Voice streaming functionality
    let mediaStream = null;
    let audioContext = null;
    let isRecording = false;
    let currentVoiceChannelId = null;
    
    // WebRTC audio streaming
    let peerConnections = new Map();
    let localAudioStream = null;
    let remoteAudioStreams = new Map();
    let audioMixer = null;

    // Handle voice channel events
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for Echo to be ready
        const checkEcho = setInterval(() => {
            if (typeof window.Echo !== 'undefined') {
                clearInterval(checkEcho);
                setupVoiceChannelListeners();
                initializeAudioSystem();
            }
        }, 100);
        
        setTimeout(() => clearInterval(checkEcho), 5000);
    });

    function initializeAudioSystem() {
        // Initialize audio context for voice streaming
        try {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            console.log('Audio system initialized');
            
            // Initialize audio mixer for multiple participants
            initializeAudioMixer();
        } catch (error) {
            console.error('Audio system initialization failed:', error);
        }
    }
    
    function initializeAudioMixer() {
        try {
            // Create audio mixer for combining multiple audio streams
            audioMixer = audioContext.createGain();
            audioMixer.connect(audioContext.destination);
            audioMixer.gain.value = 0.8; // Control overall volume
            console.log('Audio mixer initialized');
        } catch (error) {
            console.error('Audio mixer initialization failed:', error);
        }
    }
    
    function setupVoiceChannelListeners() {
        // Listen for voice channel events on the group channel
        const groupId = {{ $group->id }};
        
        console.log('Setting up voice channel listeners for group:', groupId);
        
        window.Echo.private(`group.${groupId}`)
            .subscribed(() => {
                console.log('✅ Successfully subscribed to group.' + groupId);
            })
            .listen('VoiceChannelJoined', (event) => {
                console.log('🔊 Voice channel joined event received:', event);
                showNotification(`${event.user_name} joined the voice channel`);
                // Add participant to UI without refresh
                updateParticipantList(event);
                // Trigger a refresh to ensure participant list is updated
                setTimeout(() => {
                    Livewire.dispatch('$refresh');
                }, 100);
            })
            .listen('VoiceChannelLeft', (event) => {
                console.log('🔇 Voice channel left event received:', event);
                showNotification(`${event.user_name} left the voice channel`);
                // Remove participant from UI
                removeParticipantFromUI(event.user_id);
                Livewire.dispatch('$refresh');
            })
            .listen('VoiceChannelSpeaking', (event) => {
                console.log('🎤 Voice speaking event received:', event);
                console.log('🎤 User ID:', event.user_id, 'isSpeaking:', event.is_speaking);
                // Update speaking indicators
                updateSpeakingIndicator(event.user_id, event.is_speaking);
            })
            .listen('VoiceChannelSignaling', (event) => {
                console.log('📡 Signaling event received:', event);
                // Handle WebRTC signaling
                handleSignalingMessage(event);
            })
            .error((error) => {
                console.error('❌ Voice channel listener error:', error);
            });
            
        // Also listen to individual voice channel events if we're in one
        if (currentVoiceChannelId) {
            window.Echo.private(`voice-channel.${currentVoiceChannelId}`)
                .subscribed(() => {
                    console.log('✅ Successfully subscribed to voice-channel.' + currentVoiceChannelId);
                })
                .listen('VoiceChannelJoined', (event) => {
                    console.log('🔊 Voice channel joined (direct):', event);
                    showNotification(`${event.user_name} joined the voice channel`);
                    updateParticipantList(event);
                    setTimeout(() => {
                        Livewire.dispatch('$refresh');
                    }, 100);
                })
                .listen('VoiceChannelLeft', (event) => {
                    console.log('🔇 Voice channel left (direct):', event);
                    showNotification(`${event.user_name} left the voice channel`);
                    removeParticipantFromUI(event.user_id);
                    Livewire.dispatch('$refresh');
                })
                .listen('VoiceChannelSpeaking', (event) => {
                    console.log('🎤 Voice speaking (direct):', event);
                    updateSpeakingIndicator(event.user_id, event.is_speaking);
                });
        }
    }

    function updateParticipantList(event) {
        console.log('Updating participant list for:', event.user_name, 'ID:', event.user_id);
        
        const participantsContainer = document.querySelector('.participant-avatars-container');
        if (!participantsContainer) {
            console.error('Participant container not found');
            return;
        }

        // Check if participant already exists
        const existingParticipant = participantsContainer.querySelector(`[data-user-id="${event.user_id}"]`);
        if (existingParticipant) {
            console.log('Participant already exists, skipping add');
            return;
        }

        // Add participant to UI without refresh
        const participantHtml = `
            <div class="flex items-center gap-2 px-2 py-1 bg-gray-100 dark:bg-[#2a2a2a] rounded-lg transition-all duration-300" data-user-id="${event.user_id}">
                <div class="participant-avatar bg-gradient-to-br from-[#F53003] to-[#ff4422]">
                    ${event.user_name.charAt(0).toUpperCase()}
                </div>
                <span class="text-xs font-medium text-[#1b1b18] dark:text-[#EDEDEC]">${event.user_name}</span>
                <div class="voice-indicator normal"></div>
            </div>
        `;
        
        participantsContainer.insertAdjacentHTML('beforeend', participantHtml);
        console.log('Added participant to UI:', event.user_name);
    }

    function removeParticipantFromUI(userId) {
        const participantElement = document.querySelector(`[data-user-id="${userId}"]`);
        if (participantElement) {
            participantElement.remove();
        }
    }
    
    function updateSpeakingIndicator(userId, isSpeaking) {
        console.log('🎤 updateSpeakingIndicator called for user:', userId, 'isSpeaking:', isSpeaking);
        
        // Update visual indicators for speaking users
        const participantContainers = document.querySelectorAll(`[data-user-id="${userId}"]`);
        console.log('🎤 Found', participantContainers.length, 'participant containers for user', userId);
        
        participantContainers.forEach(container => {
            // Update container background and border
            if (isSpeaking) {
                container.classList.add('participant-speaking');
                container.classList.remove('bg-gray-100', 'dark:bg-[#2a2a2a]');
            } else {
                container.classList.remove('participant-speaking');
                container.classList.add('bg-gray-100', 'dark:bg-[#2a2a2a]');
            }
            
            // Update voice indicator
            const indicators = container.querySelectorAll('.voice-indicator');
            indicators.forEach(indicator => {
                indicator.classList.remove('speaking', 'normal');
                indicator.classList.add(isSpeaking ? 'speaking' : 'normal');
            });
            
            // Update avatar ring
            const avatars = container.querySelectorAll('.participant-avatar');
            avatars.forEach(avatar => {
                avatar.classList.toggle('speaking', isSpeaking);
            });
            
            // Update or create speaking label
            let speakingLabel = container.querySelector('.speaking-label');
            if (isSpeaking) {
                if (!speakingLabel) {
                    speakingLabel = document.createElement('div');
                    speakingLabel.className = 'flex items-center gap-1 speaking-label';
                    speakingLabel.innerHTML = `
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">Speaking</span>
                    `;
                    container.appendChild(speakingLabel);
                }
            } else {
                if (speakingLabel) {
                    speakingLabel.remove();
                }
            }
        });
    }

    function showNotification(message) {
        // Create a toast notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Voice streaming functions
    async function startVoiceStream() {
        try {
            localAudioStream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                    sampleRate: 48000
                } 
            });
            
            console.log('Local voice stream started');
            
            // Connect to all participants in the voice channel
            await connectToAllParticipants();
            
            return true;
        } catch (error) {
            console.error('Failed to start voice stream:', error);
            alert('Failed to access microphone. Please check your permissions.');
            return false;
        }
    }

    function stopVoiceStream() {
        if (localAudioStream) {
            localAudioStream.getTracks().forEach(track => track.stop());
            localAudioStream = null;
        }
        
        // Close all peer connections
        peerConnections.forEach((connection, userId) => {
            connection.close();
        });
        peerConnections.clear();
        
        // Clear remote audio streams
        remoteAudioStreams.clear();
        
        console.log('Voice stream stopped and connections closed');
    }
    
    async function connectToAllParticipants() {
        const currentUserId = {{ auth()->id() }};
        const participants = document.querySelectorAll('[data-user-id]');
        
        for (const participant of participants) {
            const userId = participant.getAttribute('data-user-id');
            if (userId && userId != currentUserId && !peerConnections.has(userId)) {
                await createPeerConnection(userId);
            }
        }
    }
    
    async function createPeerConnection(userId) {
        try {
            const configuration = {
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:stun1.l.google.com:19302' }
                ]
            };
            
            const peerConnection = new RTCPeerConnection(configuration);
            
            // Add local audio stream
            if (localAudioStream) {
                localAudioStream.getTracks().forEach(track => {
                    peerConnection.addTrack(track, localAudioStream);
                });
            }
            
            // Handle incoming audio stream
            peerConnection.ontrack = (event) => {
                console.log('Received audio track from user:', userId);
                const [remoteStream] = event.streams;
                remoteAudioStreams.set(userId, remoteStream);
                playRemoteAudio(userId, remoteStream);
            };
            
            // Handle ICE candidates
            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    // Send ICE candidate to other participant via WebSocket
                    sendSignalingMessage({
                        type: 'ice-candidate',
                        targetUserId: userId,
                        candidate: event.candidate
                    });
                }
            };
            
            peerConnections.set(userId, peerConnection);
            console.log('Peer connection created for user:', userId);
            
            // Create offer
            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            
            console.log('Created offer for user:', userId, offer);
            
            // Send offer to other participant
            sendSignalingMessage({
                type: 'offer',
                targetUserId: userId,
                offer: offer
            });
            
        } catch (error) {
            console.error('Failed to create peer connection for user:', userId, error);
        }
    }
    
    function playRemoteAudio(userId, remoteStream) {
        try {
            const audioElement = document.createElement('audio');
            audioElement.srcObject = remoteStream;
            audioElement.autoplay = true;
            audioElement.volume = 0.8;
            
            // Add to remote audio streams map
            remoteAudioStreams.set(userId, { stream: remoteStream, element: audioElement });
            
            console.log('Playing audio from user:', userId);
        } catch (error) {
            console.error('Failed to play remote audio for user:', userId, error);
        }
    }
    
    function sendSignalingMessage(message) {
        // Send signaling message via Laravel Echo
        const groupId = {{ $group->id }};
        let voiceChannelId = currentVoiceChannelId;
        
        // If currentVoiceChannelId is not set, try to get it from the button or Livewire component
        if (!voiceChannelId) {
            const button = document.querySelector('button[data-voice-channel-id]');
            if (button) {
                voiceChannelId = button.getAttribute('data-voice-channel-id');
                console.log('Retrieved voice channel ID from button for signaling:', voiceChannelId);
            }
        }
        
        console.log('Sending signaling message:', message, 'to voice channel:', voiceChannelId);
        
        if (voiceChannelId) {
            // Broadcast signaling message to specific voice channel
            fetch(`/groups/${groupId}/voice-channels/${voiceChannelId}/signaling`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(message)
            })
            .then(response => {
                console.log('Signaling message sent successfully:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Signaling response:', data);
            })
            .catch(error => {
                console.error('Failed to send signaling message:', error);
            });
        } else {
            console.error('No voice channel ID available for signaling');
        }
    }
    
    async function handleSignalingMessage(event) {
        const currentUserId = {{ auth()->id() }};
        
        console.log('Processing signaling message:', event, 'Current user ID:', currentUserId);
        
        // Handle test-beep messages (sent to all participants)
        if (event.type === 'test-beep') {
            console.log('🔊 Received test beep from user:', event.from_user_id);
            console.log('🔊 Beep data:', event.beepData);
            
            // Play the beep sound locally
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(event.beepData.frequency || 800, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.2, audioContext.currentTime); // Lower volume for received beeps
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + (event.beepData.duration || 0.5));
            
            console.log('🔊 Test beep played from user:', event.from_user_id);
            return;
        }
        
        // Only handle messages intended for us (skip test-beep as it's handled above)
        if (event.target_user_id !== currentUserId && event.type !== 'test-beep') {
            console.log('Message not for us, ignoring. Target:', event.target_user_id, 'Current:', currentUserId);
            return;
        }
        
        const peerConnection = peerConnections.get(event.from_user_id);
        
        if (!peerConnection) {
            console.log('No peer connection found for user:', event.from_user_id, 'Available connections:', Array.from(peerConnections.keys()));
            return;
        }
        
        try {
            switch (event.type) {
                case 'offer':
                    console.log('Handling offer from user:', event.from_user_id);
                    await peerConnection.setRemoteDescription(new RTCSessionDescription(event.offer));
                    
                    // Create answer
                    const answer = await peerConnection.createAnswer();
                    await peerConnection.setLocalDescription(answer);
                    
                    // Send answer back
                    sendSignalingMessage({
                        type: 'answer',
                        targetUserId: event.from_user_id,
                        answer: answer
                    });
                    break;
                    
                case 'answer':
                    console.log('Handling answer from user:', event.from_user_id);
                    await peerConnection.setRemoteDescription(new RTCSessionDescription(event.answer));
                    break;
                    
                case 'ice-candidate':
                    console.log('Handling ICE candidate from user:', event.from_user_id);
                    await peerConnection.addIceCandidate(new RTCIceCandidate(event.candidate));
                    break;
            }
        } catch (error) {
            console.error('Error handling signaling message:', error);
        }
    }

    // Enhanced push-to-talk functionality
    function handlePushToTalk(event, isStart) {
        // Get voice channel ID from the button's data attribute or current session
        let voiceChannelId = currentVoiceChannelId;
        
        if (!voiceChannelId) {
            // Try to get it from the button's data attribute
            const button = event.target.closest('button[data-voice-channel-id]');
            if (button) {
                voiceChannelId = button.getAttribute('data-voice-channel-id');
                console.log('Retrieved voice channel ID from button:', voiceChannelId);
            }
        }
        
        if (!voiceChannelId) {
            console.error('No voice channel ID available for push-to-talk');
            return;
        }
        
        if (isStart && !isRecording) {
            startVoiceStream().then(success => {
                if (success) {
                    isRecording = true;
                    // Update speaking status via Livewire
                    console.log('Starting to speak...');
                    @this.call('updateSpeakingStatus', true);
                }
            });
        } else if (!isStart && isRecording) {
            stopVoiceStream();
            isRecording = false;
            // Update speaking status via Livewire
            console.log('Stopping speaking...');
            @this.call('updateSpeakingStatus', false);
        }
    }

    // Listen for Livewire events
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('voice-channel-joined', (event) => {
            console.log('Voice channel joined event received (kebab-case):', event);
            currentVoiceChannelId = event.voiceChannelId;
            console.log('Current voice channel ID set to:', currentVoiceChannelId);
            // Re-setup listeners for the specific voice channel
            setupVoiceChannelListeners();
        });
        
        Livewire.on('voiceChannelJoined', (event) => {
            console.log('Voice channel joined event received (camelCase):', event);
            currentVoiceChannelId = event.voiceChannelId;
            console.log('Current voice channel ID set to:', currentVoiceChannelId);
            // Re-setup listeners for the specific voice channel
            setupVoiceChannelListeners();
        });
        
        Livewire.on('voice-channel-left', (event) => {
            console.log('Voice channel left event received:', event);
            currentVoiceChannelId = null;
            console.log('Left voice channel, reset ID');
        });
        
        Livewire.on('start-voice-stream', async (event) => {
            console.log('🚀 Auto-starting voice stream for channel:', event.voiceChannelId);
            console.log('🚀 Event data:', event);
            currentVoiceChannelId = event.voiceChannelId;
            console.log('🚀 Set currentVoiceChannelId to:', currentVoiceChannelId);
            await startVoiceStream();
        });
        
        // Add a global debug function to check participant list
        window.debugParticipantList = function() {
            const containers = document.querySelectorAll('.participant-avatars-container');
            console.log('Found', containers.length, 'participant containers');
            containers.forEach((container, index) => {
                const participants = container.querySelectorAll('[data-user-id]');
                console.log(`Container ${index}:`, participants.length, 'participants');
                participants.forEach(p => {
                    console.log('  - User ID:', p.getAttribute('data-user-id'));
                });
            });
        };
        
        // Add a function to manually set the voice channel ID
        window.setVoiceChannelId = function(id) {
            currentVoiceChannelId = id;
            console.log('Manually set voice channel ID to:', currentVoiceChannelId);
        };
        
        // Test beep functionality
        window.sendTestBeep = async function() {
            console.log('🔊 Sending test beep...');
            console.log('🔊 Current voice channel ID:', currentVoiceChannelId);
            console.log('🔊 Peer connections count:', peerConnections.size);
            console.log('🔊 Local audio stream:', localAudioStream);
            
            if (!currentVoiceChannelId) {
                const button = document.getElementById('testBeepBtn');
                currentVoiceChannelId = button?.getAttribute('data-voice-channel-id');
                console.log('Retrieved voice channel ID from button:', currentVoiceChannelId);
            }
            
            if (!currentVoiceChannelId) {
                console.error('No voice channel ID available for test beep');
                return;
            }
            
            // Generate a test beep sound
            const beepSound = generateBeepSound();
            
            // Send the beep through WebRTC to all participants
            if (peerConnections.size > 0) {
                console.log('🔊 Sending beep to', peerConnections.size, 'participants');
                
                // Create a new audio context for the beep
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                // Configure the beep sound
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime); // 800Hz beep
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime); // 30% volume
                
                // Connect to audio output
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                // Play the beep locally
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5); // 500ms beep
                
                // Also send through WebRTC if we have a local stream
                if (localAudioStream) {
                    console.log('🔊 Beep sound generated and played locally');
                    
                    // Send a notification to other users that a beep was sent
                    const signalingMessage = {
                        type: 'test-beep',
                        targetUserId: 'all', // Send to all participants
                        beepData: {
                            frequency: 800,
                            duration: 0.5,
                            timestamp: Date.now()
                        }
                    };
                    
                    sendSignalingMessage(signalingMessage);
                } else {
                    console.log('🔊 No local audio stream available for WebRTC transmission');
                }
            } else {
                console.log('🔊 No peer connections available');
                
                // Still play the beep locally for testing
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
                
                console.log('🔊 Test beep played locally (no WebRTC connections)');
            }
        };
        
        // Generate beep sound
        function generateBeepSound() {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            return { oscillator, gainNode, audioContext };
        }
        
        // Debug function to check WebRTC connection status
        window.debugWebRTCStatus = function() {
            console.log('🔍 WebRTC Debug Status:');
            console.log('  - Current Voice Channel ID:', currentVoiceChannelId);
            console.log('  - Peer Connections:', peerConnections.size);
            console.log('  - Local Audio Stream:', localAudioStream ? 'Available' : 'Not Available');
            console.log('  - Remote Audio Streams:', remoteAudioStreams.size);
            
            peerConnections.forEach((connection, userId) => {
                console.log(`  - Peer Connection for User ${userId}:`, {
                    connectionState: connection.connectionState,
                    iceConnectionState: connection.iceConnectionState,
                    iceGatheringState: connection.iceGatheringState,
                    signalingState: connection.signalingState
                });
            });
        };
        
        // Manual voice stream start function
        window.startVoiceStreamManually = async function() {
            console.log('🎤 Manually starting voice stream...');
            
            // Get voice channel ID from button
            const button = document.getElementById('startVoiceBtn');
            const voiceChannelId = button?.getAttribute('data-voice-channel-id');
            console.log('Voice channel ID from button:', voiceChannelId);
            
            if (!voiceChannelId) {
                console.error('No voice channel ID found on button');
                return;
            }
            
            // Set the current voice channel ID
            currentVoiceChannelId = voiceChannelId;
            console.log('Set currentVoiceChannelId to:', currentVoiceChannelId);
            
            // Start the voice stream
            await startVoiceStream();
        };
    });

    // Expose functions globally for Livewire components
    window.startVoiceStream = startVoiceStream;
    window.stopVoiceStream = stopVoiceStream;
    window.handlePushToTalk = handlePushToTalk;
</script>
@endpush