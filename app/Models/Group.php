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
        'settings',
        'invite_code',
        'max_members',
        'is_public',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'settings' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'joined_at', 'permissions')
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

    public function voiceChannels(): HasMany
    {
        return $this->hasMany(VoiceChannel::class);
    }

    public function knowledgeFiles(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeFile::class, 'group_files')
            ->withPivot('uploaded_by')
            ->withTimestamps();
    }

    public function generateInviteCode(): string
    {
        $code = strtoupper(substr(md5(uniqid()), 0, 8));
        $this->update(['invite_code' => $code]);

        return $code;
    }

    public function getInviteLink(): string
    {
        return route('groups.join', ['code' => $this->invite_code]);
    }

    public function canUserJoin(User $user): bool
    {
        // Check if user is already a member
        if ($this->members()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check if group has reached max members
        if ($this->max_members && $this->members()->count() >= $this->max_members) {
            return false;
        }

        return true;
    }

    public function addMember(User $user, string $role = 'member'): void
    {
        if ($this->canUserJoin($user)) {
            $this->members()->attach($user->id, [
                'role' => $role,
                'joined_at' => now(),
                'permissions' => json_encode($this->getDefaultPermissions($role)),
            ]);
        }
    }

    public function removeMember(User $user): void
    {
        $this->members()->detach($user->id);
    }

    public function updateMemberRole(User $user, string $role): void
    {
        $this->members()->updateExistingPivot($user->id, [
            'role' => $role,
            'permissions' => json_encode($this->getDefaultPermissions($role)),
        ]);
    }

    protected function getDefaultPermissions(string $role): array
    {
        return match ($role) {
            'admin' => ['manage_group', 'invite_members', 'upload_files', 'delete_files', 'manage_settings'],
            'moderator' => ['invite_members', 'upload_files', 'delete_files'],
            'member' => ['upload_files'],
            default => [],
        };
    }

    public function userHasPermission(User $user, string $permission): bool
    {
        $member = $this->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return false;
        }

        $permissions = $member->pivot->permissions ?? [];

        return in_array($permission, $permissions);
    }

    public function getMemberCount(): int
    {
        return $this->members()->count();
    }

    public function getFileCount(): int
    {
        return $this->files()->count();
    }

    public function getMessageCount(): int
    {
        return $this->messages()->count();
    }

    public function getRecentActivity(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->messages()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
