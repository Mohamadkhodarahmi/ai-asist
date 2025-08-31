<?php
namespace App\Jobs;

// ... other imports
use App\Models\KnowledgeFile;
use App\Services\EmbeddingService; // A service you will create
use Spatie\PdfToText\Pdf;

class ProcessKnowledgeFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public KnowledgeFile $file) {}

    public function handle(EmbeddingService $embeddingService): void
    {
        // 1. Update status to 'processing'
        $this->file->update(['status' => 'processing']);

        try {
            // 2. Parse text from the file
            $text = Pdf::getText(storage_path('app/' . $this->file->storage_path));

            // 3. Chunk the text (this is a simplified example)
            $chunks = str_split($text, 1000); // Simple chunking, can be improved

            // 4. Generate embeddings for each chunk
            $embeddings = $embeddingService->generateEmbeddings($chunks);

            // 5. Store chunks and their embeddings in the database
            foreach ($chunks as $index => $chunkContent) {
                $this->file->textChunks()->create([
                    'content' => $chunkContent,
                    'embedding' => $embeddings[$index],
                ]);
            }

            // 6. Mark as completed
            $this->file->update(['status' => 'completed']);
        } catch (\Exception $e) {
            $this->file->update(['status' => 'failed']);
            // Log the error
        }
    }
}
