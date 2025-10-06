<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAnalytic extends Model
{
    protected $fillable = [
        'user_id',
        'business_id',
        'document_name',
        'document_type',
        'file_size_bytes',
        'pages_count',
        'questions_asked',
        'last_accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_accessed_at' => 'datetime',
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
}
