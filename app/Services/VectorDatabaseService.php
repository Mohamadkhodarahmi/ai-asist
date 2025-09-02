<?php

namespace App\Services;

use App\Models\TextChunk;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection; // Import the base Collection
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class VectorDatabaseService
{
    /**
     * Adds or updates vectors in Pinecone.
     * We change the type hint here to the more general Support\Collection.
     *
     * @param \Illuminate\Support\Collection $chunks
     * @param array $embeddings
     * @return void
     */
    public function upsert(Collection $chunks, array $embeddings): void
    {
        $vectors = [];
        foreach ($chunks as $index => $chunk) {
            $vectors[] = [
                'id' => (string)$chunk->id, // Pinecone requires a string ID
                'values' => $embeddings[$index],
                'metadata' => [
                    'content' => $chunk->content,
                    'file_id' => $chunk->knowledge_file_id,
                ],
            ];
        }

        Http::withHeaders([
            'Api-Key' => config('services.pinecone.api_key'),
        ])->post(config('services.pinecone.host') . '/vectors/upsert', [
            'vectors' => $vectors,
        ]);
    }

    /**
     * Finds the most similar text chunks from Pinecone with caching.
     *
     * @param array $queryVector
     * @param int $businessId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findSimilarChunks(array $queryVector, int $businessId, int $limit = 3): EloquentCollection
    {
        $vectorKey = md5(json_encode($queryVector));
        $cacheKey = "similar_chunks:{$businessId}:{$vectorKey}";

        return Cache::remember($cacheKey, 3600, function () use ($queryVector, $businessId, $limit) {
            $response = Http::withHeaders([
                'Api-Key' => config('services.pinecone.api_key'),
            ])
            ->timeout(10)
            ->post(config('services.pinecone.host') . '/query', [
                'vector' => $queryVector,
                'topK' => $limit,
                'includeMetadata' => true,
                'filter' => [
                    'business_id' => $businessId
                ]
            ]);

            $matches = $response->json('matches');
            if (empty($matches)) {
                return new EloquentCollection();
            }

            $chunkIds = collect($matches)->pluck('id');
            
            // Eager load any relationships and order by the order of IDs we got from vector search
            return TextChunk::whereIn('id', $chunkIds)
                ->orderByRaw("FIELD(id, " . $chunkIds->join(',') . ")")
                ->get();
        });
    }
}
