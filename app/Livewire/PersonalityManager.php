<?php

namespace App\Livewire;

use App\Models\AiPersonality;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class PersonalityManager extends Component
{
    public string $name = '';

    public string $tone = 'professional';

    public string $style = 'helpful';

    public string $system_prompt = '';

    public string $greeting_message = '';

    public bool $showCreateForm = false;

    public ?int $editingId = null;

    public array $toneOptions = [
        'professional' => 'Professional',
        'friendly' => 'Friendly',
        'casual' => 'Casual',
        'formal' => 'Formal',
        'enthusiastic' => 'Enthusiastic',
    ];

    public array $styleOptions = [
        'helpful' => 'Helpful',
        'concise' => 'Concise',
        'detailed' => 'Detailed',
        'creative' => 'Creative',
        'analytical' => 'Analytical',
    ];

    protected array $rules = [
        'name' => 'required|string|max:255',
        'tone' => 'required|in:professional,friendly,casual,formal,enthusiastic',
        'style' => 'required|in:helpful,concise,detailed,creative,analytical',
        'system_prompt' => 'nullable|string|max:2000',
        'greeting_message' => 'nullable|string|max:500',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        // Check if user has access to personality customization (Starter+ plans)
        if (! $this->hasPersonalityAccess($user)) {
            abort(403, 'AI Personality customization is only available for Starter, Pro and Business plans.');
        }
    }

    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;
        $this->resetForm();
    }

    public function createPersonality(): void
    {
        $this->validate();

        $user = Auth::user();

        AiPersonality::create([
            'user_id' => $user->id,
            'name' => $this->name,
            'tone' => $this->tone,
            'style' => $this->style,
            'system_prompt' => $this->system_prompt ?: null,
            'greeting_message' => $this->greeting_message ?: null,
            'is_active' => false,
        ]);

        $this->resetForm();
        $this->showCreateForm = false;

        session()->flash('message', 'AI Personality created successfully!');
    }

    public function editPersonality(int $id): void
    {
        $personality = AiPersonality::where('user_id', Auth::id())->findOrFail($id);

        $this->editingId = $id;
        $this->name = $personality->name;
        $this->tone = $personality->tone;
        $this->style = $personality->style;
        $this->system_prompt = $personality->system_prompt ?? '';
        $this->greeting_message = $personality->greeting_message ?? '';
        $this->showCreateForm = true;
    }

    public function updatePersonality(): void
    {
        $this->validate();

        $personality = AiPersonality::where('user_id', Auth::id())->findOrFail($this->editingId);

        $personality->update([
            'name' => $this->name,
            'tone' => $this->tone,
            'style' => $this->style,
            'system_prompt' => $this->system_prompt ?: null,
            'greeting_message' => $this->greeting_message ?: null,
        ]);

        $this->resetForm();
        $this->showCreateForm = false;

        session()->flash('message', 'AI Personality updated successfully!');
    }

    public function activatePersonality(int $id): void
    {
        $personality = AiPersonality::where('user_id', Auth::id())->findOrFail($id);
        $personality->activate();

        session()->flash('message', "'{$personality->name}' is now your active AI personality!");
    }

    public function deletePersonality(int $id): void
    {
        $personality = AiPersonality::where('user_id', Auth::id())->findOrFail($id);

        if ($personality->is_active) {
            session()->flash('error', 'Cannot delete the active personality. Please activate another one first.');

            return;
        }

        $personality->delete();
        session()->flash('message', 'AI Personality deleted successfully!');
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->tone = 'professional';
        $this->style = 'helpful';
        $this->system_prompt = '';
        $this->greeting_message = '';
        $this->editingId = null;
    }

    private function hasPersonalityAccess($user): bool
    {
        $planSlug = $user->plan?->slug;

        return in_array($planSlug, ['starter', 'pro', 'business']);
    }

    public function render()
    {
        $personalities = AiPersonality::where('user_id', Auth::id())
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.personality-manager', [
            'personalities' => $personalities,
        ]);
    }
}
