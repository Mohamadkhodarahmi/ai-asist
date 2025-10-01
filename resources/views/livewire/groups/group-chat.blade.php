<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300 rounded-lg">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-800/50 text-red-800 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Chat Area -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] h-[calc(100vh-200px)] flex flex-col">
                    <!-- Header -->
                    <div class="p-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <h2 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $group->name }}</h2>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $group->description }}</p>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        @forelse ($group->messages as $msg)
                            <div class="flex {{ $msg->is_ai_response ? 'justify-start' : ($msg->user_id == Auth::id() ? 'justify-end' : 'justify-start') }}">
                                <div class="max-w-[70%]">
                                    @if (!$msg->is_ai_response && $msg->user)
                                        <div class="text-xs text-gray-800 dark:text-gray-200 mb-1">{{ $msg->user->name }}</div>
                                    @endif
                                    @if ($msg->is_ai_response)
                                        <div class="text-xs text-[#F53003] dark:text-[#FF4433] mb-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                            AI Assistant
                                        </div>
                                    @endif
                                    <div class="px-4 py-2 rounded-2xl {{ $msg->is_ai_response ? 'bg-[#F53003]/10 dark:bg-[#FF4433]/10 text-[#1b1b18] dark:text-[#EDEDEC]' : ($msg->user_id == Auth::id() ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC]') }}">
                                        <p class="text-sm whitespace-pre-wrap" style="{{ $msg->user_id == Auth::id() ? 'color: #000000;' : 'color: #1b1b18 !important;' }}">{{ $msg->message }}</p>
                                    </div>
                                    <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $msg->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-[#706f6c] dark:text-[#A1A09A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <p class="text-[#706f6c] dark:text-[#A1A09A]">No messages yet. Start the conversation!</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Input Area -->
                    <form wire:submit.prevent="sendMessage" class="p-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <div class="flex gap-2">
                            <textarea wire:model="message" rows="2" class="flex-1 px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-[#F53003] resize-none" placeholder="Type your message or question..."></textarea>
                            <input type="submit" value="Send" class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold self-end cursor-pointer" style="background-color: red !important; color: white !important; border: none !important; padding: 8px 12px !important; border-radius: 6px !important; font-weight: bold !important; cursor: pointer !important; min-width: 80px !important;">
                        </div>
                        @error('message') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Members -->
                <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg p-4 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h3 class="font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Members ({{ $group->members->count() }})</h3>
                    
                    <!-- Invite Member Form -->
                    <form wire:submit.prevent="inviteMember" class="mb-4">
                        <input type="email" wire:model.defer="inviteEmail" placeholder="Enter user email..." class="w-full text-sm px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-[#F53003]">
                        @error('inviteEmail') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <input type="submit" value="+ Invite Member" class="mt-2 w-full py-2 px-3 bg-red-600 text-white rounded-lg text-sm font-semibold cursor-pointer" style="background-color: red !important; color: white !important; border: none !important; padding: 8px 12px !important; border-radius: 6px !important; font-weight: bold !important; cursor: pointer !important;">
                    </form>

                    <div class="space-y-2">
                        @foreach ($group->members as $member)
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-[#F53003] to-[#FF4433] rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <span class="flex-1 text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $member->name }}</span>
                                @if ($member->pivot && $member->pivot->role === 'owner')
                                    <span class="text-xs bg-[#F53003]/10 text-[#F53003] dark:bg-[#FF4433]/10 dark:text-[#FF4433] px-2 py-1 rounded-full">Owner</span>
                                @else
                                    @php
                                        $currentUserMember = $group->members->where('user_id', Auth::id())->first();
                                        $isOwner = $currentUserMember && $currentUserMember->pivot && $currentUserMember->pivot->role === 'owner';
                                    @endphp
                                    @if ($isOwner || $member->id === Auth::id())
                                        <button wire:click="removeMember({{ $member->id }})" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Remove member">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Files -->
                <div class="bg-white dark:bg-[#161615] rounded-xl shadow-lg p-4 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h3 class="font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Learning Materials</h3>
                    
                    <form wire:submit.prevent="uploadFileToGroup" class="mb-4">
                        <input type="file" wire:model="uploadFile" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#F53003]/10 file:text-[#F53003] hover:file:bg-[#F53003]/20">
                        @error('uploadFile') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        <input type="submit" value="Upload File" class="mt-2 w-full py-2 px-3 bg-red-600 text-white rounded-lg text-sm font-semibold cursor-pointer" style="background-color: red !important; color: white !important; border: none !important; padding: 8px 12px !important; border-radius: 6px !important; font-weight: bold !important; cursor: pointer !important;">
                    </form>

                    <div class="space-y-2">
                        @forelse ($group->files as $file)
                            <div class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-[#3E3E3A] rounded-lg">
                                <svg class="w-4 h-4 text-[#F53003] dark:text-[#FF4433]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span class="flex-1 text-[#1b1b18] dark:text-[#EDEDEC] truncate">{{ $file->knowledgeFile->original_name }}</span>
                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ ucfirst($file->knowledgeFile->status) }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] text-center py-4">No files uploaded yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
