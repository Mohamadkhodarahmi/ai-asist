<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class TeamManagement extends Component
{
    public ?Group $group = null;
    public array $members = [];
    public array $pendingInvites = [];
    
    // Invite form
    public string $inviteEmail = '';
    public string $inviteRole = 'member';
    public string $inviteMessage = '';
    
    // Group settings
    public string $groupName = '';
    public string $groupDescription = '';
    public int $maxMembers = 0;
    public bool $isPublic = false;
    
    // Member management
    public ?int $selectedMemberId = null;
    public string $newRole = '';
    
    public bool $showInviteForm = false;
    public bool $showSettings = false;
    public bool $showMemberManagement = false;

    public function mount(?int $groupId = null)
    {
        $this->loadGroup($groupId);
        $this->loadMembers();
        $this->loadSettings();
    }

    protected function loadGroup(?int $groupId): void
    {
        if ($groupId) {
            $this->group = Group::where('owner_id', Auth::id())
                ->orWhereHas('members', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->findOrFail($groupId);
        } else {
            $this->group = Group::where('owner_id', Auth::id())
                ->orWhereHas('members', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->first();
        }
    }

    protected function loadMembers(): void
    {
        if (!$this->group) {
            return;
        }

        $this->members = $this->group->members()
            ->withPivot('role', 'joined_at', 'permissions')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'role' => $member->pivot->role,
                    'joined_at' => $member->pivot->joined_at,
                    'permissions' => $member->pivot->permissions ?? [],
                ];
            })
            ->toArray();
    }

    protected function loadSettings(): void
    {
        if (!$this->group) {
            return;
        }

        $this->groupName = $this->group->name;
        $this->groupDescription = $this->group->description ?? '';
        $this->maxMembers = $this->group->max_members ?? 0;
        $this->isPublic = $this->group->is_public ?? false;
    }

    public function inviteMember(): void
    {
        $this->validate([
            'inviteEmail' => 'required|email|exists:users,email',
            'inviteRole' => 'required|in:member,moderator,admin',
        ]);

        $user = User::where('email', $this->inviteEmail)->first();
        
        if (!$user) {
            $this->addError('inviteEmail', 'User not found.');
            return;
        }

        if (!$this->group->canUserJoin($user)) {
            $this->addError('inviteEmail', 'User cannot join this group.');
            return;
        }

        $this->group->addMember($user, $this->inviteRole);
        
        $this->loadMembers();
        $this->resetInviteForm();
        
        session()->flash('success', 'Member invited successfully!');
    }

    public function removeMember(int $userId): void
    {
        if (!$this->canManageMembers()) {
            session()->flash('error', 'You do not have permission to remove members.');
            return;
        }

        $user = User::findOrFail($userId);
        $this->group->removeMember($user);
        
        $this->loadMembers();
        session()->flash('success', 'Member removed successfully!');
    }

    public function updateMemberRole(int $userId, string $role): void
    {
        if (!$this->canManageMembers()) {
            session()->flash('error', 'You do not have permission to update member roles.');
            return;
        }

        $user = User::findOrFail($userId);
        $this->group->updateMemberRole($user, $role);
        
        $this->loadMembers();
        session()->flash('success', 'Member role updated successfully!');
    }

    public function generateInviteCode(): void
    {
        if (!$this->canManageGroup()) {
            session()->flash('error', 'You do not have permission to generate invite codes.');
            return;
        }

        $code = $this->group->generateInviteCode();
        session()->flash('success', "Invite code generated: {$code}");
    }

    public function updateGroupSettings(): void
    {
        if (!$this->canManageGroup()) {
            session()->flash('error', 'You do not have permission to update group settings.');
            return;
        }

        $this->validate([
            'groupName' => 'required|string|max:255',
            'groupDescription' => 'nullable|string|max:1000',
            'maxMembers' => 'nullable|integer|min:0',
            'isPublic' => 'boolean',
        ]);

        $this->group->update([
            'name' => $this->groupName,
            'description' => $this->groupDescription,
            'max_members' => $this->maxMembers ?: null,
            'is_public' => $this->isPublic,
        ]);

        session()->flash('success', 'Group settings updated successfully!');
    }

    protected function resetInviteForm(): void
    {
        $this->inviteEmail = '';
        $this->inviteRole = 'member';
        $this->inviteMessage = '';
        $this->showInviteForm = false;
    }

    public function canManageGroup(): bool
    {
        if (!$this->group) {
            return false;
        }

        return $this->group->owner_id === Auth::id() || 
               $this->group->userHasPermission(Auth::user(), 'manage_group');
    }

    public function canManageMembers(): bool
    {
        if (!$this->group) {
            return false;
        }

        return $this->group->owner_id === Auth::id() || 
               $this->group->userHasPermission(Auth::user(), 'invite_members');
    }

    public function canUploadFiles(): bool
    {
        if (!$this->group) {
            return false;
        }

        return $this->group->userHasPermission(Auth::user(), 'upload_files');
    }

    public function getAvailableRoles(): array
    {
        return [
            'member' => 'Member',
            'moderator' => 'Moderator',
            'admin' => 'Admin',
        ];
    }

    public function getRolePermissions(string $role): array
    {
        return match ($role) {
            'admin' => ['Manage group', 'Invite members', 'Upload files', 'Delete files', 'Manage settings'],
            'moderator' => ['Invite members', 'Upload files', 'Delete files'],
            'member' => ['Upload files'],
            default => [],
        };
    }

    public function getGroupStats(): array
    {
        if (!$this->group) {
            return [];
        }

        return [
            'member_count' => $this->group->getMemberCount(),
            'file_count' => $this->group->getFileCount(),
            'message_count' => $this->group->getMessageCount(),
            'max_members' => $this->group->max_members,
            'is_public' => $this->group->is_public,
            'invite_code' => $this->group->invite_code,
        ];
    }

    public function render()
    {
        return view('livewire.team-management', [
            'groupStats' => $this->getGroupStats(),
            'availableRoles' => $this->getAvailableRoles(),
        ]);
    }
}