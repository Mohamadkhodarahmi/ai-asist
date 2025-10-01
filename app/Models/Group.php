<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(GroupFile::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }

    public function knowledgeFiles(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeFile::class, 'group_files')
            ->withPivot('uploaded_by')
            ->withTimestamps();
    }
}
