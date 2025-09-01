<?php

namespace App\Jobs;

use App\Models\KnowledgeFile;
use App\Services\EmbeddingService;
use App\Services\FileParsingService;
use App\Services\VectorDatabaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProcessKnowledgeFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\KnowledgeFile $file The file to be processed.
     */
    public function __construct(public KnowledgeFile $file)
    {
        //
    }

    /**
     * Execute the job.
     * This method orchestrates the entire file processing pipeline.
     *
     * @param \App\Services\EmbeddingService $embeddingService
     * @param \App\Services\VectorDatabaseService $vectorDbService
     * @param \App\Services\FileParsingService $fileParsingService
     * @return void
     */
    public function handle(
        EmbeddingService $embeddingService,
        VectorDatabaseService $vectorDbService,
        FileParsingService $fileParsingService
    ): void {
        $this->file->update(['status' => 'processing']);

        try {
            $text = $fileParsingService->getTextFromFile($this->file->storage_path);
            $rawChunks = str_split($text, 1000);

            foreach ($rawChunks as $chunkContent) {
                // This loop is the main change.
                // We process one chunk at a time.

                // 1. Save the single chunk to the local DB to get an ID.
                $savedChunk = $this->file->textChunks()->create(['content' => $chunkContent]);

                // 2. Generate an embedding for this single chunk.
                $embedding = $embeddingService->generateEmbedding($savedChunk->content);

                // 3. Upsert this single embedding to Pinecone.
                $vectorDbService->upsert(collect([$savedChunk]), [$embedding]);
            }

            $this->file->update(['status' => 'completed']);

        } catch (\Exception $e) {
            $this->file->update(['status' => 'failed']);
            Log::error('Failed to process knowledge file: ' . $e->getMessage(), [
                'file_id' => $this->file->id,
                'exception' => $e
            ]);
        }
    }
}
