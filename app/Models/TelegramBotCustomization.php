<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramBotCustomization extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_bot_id',
        'bot_name',
        'welcome_message',
        'help_message',
        'error_message',
        'personality_settings',
        'keyboard_layout',
        'commands',
        'quick_replies',
        'language',
        'theme_settings',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'personality_settings' => 'array',
            'keyboard_layout' => 'array',
            'commands' => 'array',
            'quick_replies' => 'array',
            'theme_settings' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function telegramBot(): BelongsTo
    {
        return $this->belongsTo(TelegramBot::class);
    }

    public function getPersonalityTone(): string
    {
        return $this->personality_settings['tone'] ?? 'professional';
    }

    public function getPersonalityStyle(): string
    {
        return $this->personality_settings['style'] ?? 'helpful';
    }

    public function getWelcomeMessage(): string
    {
        return $this->welcome_message ?? 'Hello! How can I help you today?';
    }

    public function getHelpMessage(): string
    {
        return $this->help_message ?? 'I can help you with questions about your documents. Just ask me anything!';
    }

    public function getErrorMessage(): string
    {
        return $this->error_message ?? 'Sorry, I encountered an error. Please try again.';
    }

    public function getCommands(): array
    {
        return $this->commands ?? [];
    }

    public function getQuickReplies(): array
    {
        return $this->quick_replies ?? [];
    }

    public function getKeyboardLayout(): array
    {
        return $this->keyboard_layout ?? [];
    }
}