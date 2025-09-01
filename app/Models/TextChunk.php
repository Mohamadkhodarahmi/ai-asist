<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pgvector\Vector;

class TextChunk extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'knowledge_file_id',
        'content',
        'embedding',
    ];

    /**
     * The attributes that should be cast.
     * This is crucial for pgvector. It tells Laravel to automatically
     * convert the 'embedding' column from the database string
     * into a Vector object and back.
     *
     * @var array
     */
    protected $casts = [
        'embedding' => Vector::class,
    ];

    /**
     * Get the knowledge file that this chunk belongs to.
     */
    public function knowledgeFile(): BelongsTo
    {
        return $this->belongsTo(KnowledgeFile::class);
    }
}
