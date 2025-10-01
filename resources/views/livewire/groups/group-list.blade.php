<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Learning Groups</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A] mt-2">Collaborate with others and learn together with AI assistance</p>
            </div>
            <button wire:click="toggleCreateForm" class="px-6 py-3 bg-[#F53003] text-white rounded-xl font-semibold hover:bg-[#c41e00] transition-all shadow-lg">
                {{ $showCreateForm ? 'Cancel' : '+ New Group' }}
            </button>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if ($showCreateForm)
            <div class="mb-8 bg-white dark:bg-[#161615] rounded-xl shadow-lg p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h2 class="text-xl font-bold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Create New Group</h2>
                <form wire:submit.prevent="createGroup">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 text-[#1b1b18] dark:text-[#EDEDEC]">Group Name</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-[#F53003]" placeholder="e.g., Physics Study Group">
                            @error('name') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 text-[#1b1b18] dark:text-[#EDEDEC]">Description (Optional)</label>
                            <textarea wire:model="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-[#F53003]" placeholder="What will this group study?"></textarea>
                            @error('description') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="px-6 py-2 bg-[#F53003] text-white rounded-lg font-semibold hover:bg-[#c41e00] transition-colors">
                            <span wire:loading.remove>Create Group</span>
                            <span wire:loading>Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($myGroups as $group)
                <a href="{{ route('groups.chat', $group) }}" class="block bg-white dark:bg-[#161615] rounded-xl shadow-lg p-6 border border-[#e3e3e0] dark:border-[#3E3E3A] hover:shadow-xl hover:-translate-y-1 transition-all">
                    <h3 class="text-lg font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">{{ $group->name }}</h3>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm mb-4 line-clamp-2">{{ $group->description ?: 'No description' }}</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[#706f6c] dark:text-[#A1A09A]">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            {{ $group->members_count }} member(s)
                        </span>
                        <span class="text-[#F53003] dark:text-[#FF4433] font-medium">Open →</span>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12 bg-white dark:bg-[#161615] rounded-xl border-2 border-dashed border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <svg class="w-16 h-16 mx-auto text-[#706f6c] dark:text-[#A1A09A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <h3 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">No Groups Yet</h3>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">Create your first learning group to start collaborating with AI</p>
                    <button wire:click="toggleCreateForm" class="inline-block px-6 py-2 bg-[#F53003] text-white rounded-lg font-semibold hover:bg-[#c41e00] transition-colors">
                        Create Group
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>
