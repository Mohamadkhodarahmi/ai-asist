<div class="py-12">
    <div class="w-full max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="upload-form p-6 mb-8 bg-white dark:bg-[#161615] rounded-lg shadow-md border border-[#e3e3e0] dark:border-[#3E3E3A]">
            <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Upload New Knowledge File</h2>
            @if (session()->has('message'))
                <div class="mb-4 p-3 bg-green-100 dark:bg-green-800/50 text-green-800 dark:text-green-300 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif
            <form wire:submit.prevent="save" class="flex flex-col gap-4">
                <div>
                    <input type="file" wire:model="document" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#f53003]/10 file:text-[#f53003] hover:file:bg-[#f53003]/20 dark:file:bg-[#FF4433]/10 dark:file:text-[#FF4433] dark:hover:file:bg-[#FF4433]/20">
                    @error('document') <span class="text-red-600 text-sm mt-2">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="py-2 px-4 bg-[#f53003] text-white rounded-md hover:bg-[#c41e00] transition-colors font-semibold shadow self-start">
                    <span wire:loading.remove wire:target="save">Upload File</span>
                    <span wire:loading wire:target="save">Uploading...</span>
                </button>
            </form>
        </div>
        <div class="file-list p-6 bg-white dark:bg-[#161615] rounded-lg shadow-md border border-[#e3e3e0] dark:border-[#3E3E3A]">
            <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Your Files</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#f2f2f2] dark:bg-[#3E3E3A]/50 text-left">
                        <tr>
                            <th class="p-3">File Name</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Uploaded At</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($business->knowledgeFiles as $file)
                        <tr class="border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                            <td class="p-3">{{ $file->original_name }}</td>
                            <td class="p-3">{{ ucfirst($file->status) }}</td>
                            <td class="p-3">{{ $file->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-3 text-center text-[#706f6c] dark:text-[#A1A09A]">You have not uploaded any files yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
