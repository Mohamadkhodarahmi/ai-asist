<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'original_name',
        'storage_path',
        'status',
    ];

    /**
     * Get the business that owns the knowledge file.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the text chunks for the knowledge file.
     */
    public function textChunks(): HasMany
    {
        // We will create the TextChunk model later.
        return $this->hasMany(TextChunk::class);
    }
}
