
    <div> {{-- ✅ Single root wrapper for Livewire --}}
        {{-- Main container --}}
        <div style="width: 80%; margin: 2rem auto;">

            {{-- File Upload Form --}}
            <div class="upload-form" style="padding: 2rem; border: 1px solid #eee; border-radius: 8px;">
                <h2>Upload New Knowledge File</h2>

                {{-- Success Message --}}
                @if (session()->has('message'))
                    <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save">
                    <input type="file" wire:model="document">

                    @error('document')
                    <span style="color: red;">{{ $message }}</span>
                    @enderror

                    <button type="submit">Upload</button>
                </form>
            </div>

            {{-- File List --}}
            <div class="file-list" style="margin-top: 2rem;">
                <h2>Your Files</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">File Name</th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Status</th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Uploaded At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($business->knowledgeFiles as $file)
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $file->original_name }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ ucfirst($file->status) }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $file->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: center;">You have not uploaded any files yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

