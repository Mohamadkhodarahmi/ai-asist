<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupList extends Component
{
    public $name = '';

    public $description = '';

    public $showCreateForm = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public function createGroup(): void
    {
        $this->validate();

        $group = Group::create([
            'name' => $this->name,
            'description' => $this->description,
            'owner_id' => Auth::id(),
            'is_active' => true,
        ]);

        // Add owner as member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'owner',
        ]);

        $this->reset(['name', 'description', 'showCreateForm']);
        session()->flash('message', 'Group created successfully!');
    }

    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;
        $this->reset(['name', 'description']);
    }

    public function render()
    {
        $myGroups = Auth::user()
            ->groups()
            ->withCount('members')
            ->latest()
            ->get();

        return view('livewire.groups.group-list', [
            'myGroups' => $myGroups,
        ])->layout('layouts.app');
    }
}
