<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupFile extends Model
{
    protected $fillable = [
        'group_id',
        'knowledge_file_id',
        'uploaded_by',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function knowledgeFile(): BelongsTo
    {
        return $this->belongsTo(KnowledgeFile::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
