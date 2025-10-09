<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAnalytic extends Model
{
    protected $fillable = [
        'user_id',
        'business_id',
        'knowledge_file_id',
        'document_name',
        'document_type',
        'file_size_bytes',
        'pages_count',
        'questions_asked',
        'total_queries',
        'successful_queries',
        'failed_queries',
        'avg_response_time_ms',
        'search_terms',
        'uploaded_at',
        'last_accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_accessed_at' => 'datetime',
            'uploaded_at' => 'datetime',
            'search_terms' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function knowledgeFile(): BelongsTo
    {
        return $this->belongsTo(KnowledgeFile::class);
    }
}
