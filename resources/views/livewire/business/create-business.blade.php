<div class="p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-semibold mb-6">Create New Business</h2>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Business Name</label>
            <input type="text" 
                   id="name" 
                   wire:model="name" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Enter business name">
            @error('name') 
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Create Business
            </button>
        </div>
    </form>

    @if (session()->has('message'))
        <div class="mt-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('message') }}
        </div>
    @endif
</div>
