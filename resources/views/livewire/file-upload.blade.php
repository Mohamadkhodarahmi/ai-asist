<div class="w-full max-w-2xl mx-auto p-6 bg-white dark:bg-[#161615] rounded-lg shadow-lg animate-fade-in flex flex-col gap-8">
    <div class="upload-form p-6 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg bg-[#FDFDFC] dark:bg-[#0a0a0a]">
        <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Upload New Knowledge File</h2>
        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('message') }}
            </div>
        @endif
        <form wire:submit.prevent="save" class="flex flex-col gap-4">
            <input type="file" wire:model="document" class="file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-[#f53003] file:text-white file:font-semibold file:hover:bg-[#c41e00] file:transition-colors">
            @error('document') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            <button type="submit" class="py-2 px-4 bg-[#f53003] text-white rounded hover:bg-[#c41e00] transition-colors font-semibold shadow self-start">Upload</button>
        </form>
    </div>
    <div class="file-list">
        <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Your Files</h2>
        <table class="w-full border-collapse rounded overflow-hidden">
            <thead>
            <tr class="bg-[#f2f2f2] dark:bg-[#3E3E3A] text-left">
                <th class="p-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">File Name</th>
                <th class="p-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">Status</th>
                <th class="p-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">Uploaded At</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($business->knowledgeFiles as $file)
                <tr class="odd:bg-white even:bg-[#FDFDFC] dark:odd:bg-[#161615] dark:even:bg-[#0a0a0a]">
                    <td class="p-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">{{ $file->original_name }}</td>
                    <td class="p-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">{{ ucfirst($file->status) }}</td>
                    <td class="p-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">{{ $file->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A] text-center text-[#706f6c] dark:text-[#A1A09A]">You have not uploaded any files yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
