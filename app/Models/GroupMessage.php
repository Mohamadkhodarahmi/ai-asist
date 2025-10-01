<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupMessage extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'message',
        'is_ai_response',
        'parent_message_id',
    ];

    protected $casts = [
        'is_ai_response' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(GroupMessage::class, 'parent_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(GroupMessage::class, 'parent_message_id');
    }
}
