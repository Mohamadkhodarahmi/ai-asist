<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiPersonality extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'tone',
        'style',
        'system_prompt',
        'greeting_message',
        'personality_traits',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'personality_traits' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activate(): void
    {
        // Deactivate all other personalities for this user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        // Activate this personality
        $this->update(['is_active' => true]);
    }

    public function generateSystemPrompt(): string
    {
        $basePrompt = 'You are a helpful AI assistant.';

        if ($this->system_prompt) {
            return $this->system_prompt;
        }

        // Generate prompt based on tone and style
        $toneInstructions = $this->getToneInstructions();
        $styleInstructions = $this->getStyleInstructions();

        return "{$basePrompt} {$toneInstructions} {$styleInstructions}";
    }

    private function getToneInstructions(): string
    {
        return match ($this->tone) {
            'professional' => 'Maintain a professional and business-appropriate tone.',
            'friendly' => 'Be warm, approachable, and conversational in your responses.',
            'casual' => 'Use a relaxed, informal tone like talking to a friend.',
            'formal' => 'Use formal language and maintain a respectful, academic tone.',
            'enthusiastic' => 'Be energetic, positive, and encouraging in your responses.',
            default => 'Use a balanced, helpful tone.',
        };
    }

    private function getStyleInstructions(): string
    {
        return match ($this->style) {
            'helpful' => 'Focus on being as helpful as possible and providing actionable advice.',
            'concise' => 'Keep responses brief and to the point while maintaining clarity.',
            'detailed' => 'Provide comprehensive, thorough explanations with examples when appropriate.',
            'creative' => 'Think outside the box and offer creative solutions and perspectives.',
            'analytical' => 'Approach problems methodically and provide data-driven insights.',
            default => 'Provide clear, balanced responses.',
        };
    }
}
