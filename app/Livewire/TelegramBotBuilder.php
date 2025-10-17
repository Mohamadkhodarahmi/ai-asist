<?php

namespace App\Livewire;

use App\Models\TelegramBot;
use App\Models\TelegramBotCustomization;
use App\Models\TelegramBotTemplate;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class TelegramBotBuilder extends Component
{
    public ?TelegramBot $bot = null;
    public ?TelegramBotCustomization $customization = null;
    
    // Bot Basic Settings
    public string $bot_name = '';
    public string $welcome_message = '';
    public string $help_message = '';
    public string $error_message = '';
    public string $language = 'en';
    
    // Personality Settings
    public string $personality_tone = 'professional';
    public string $personality_style = 'helpful';
    public array $personality_traits = [];
    
    // Keyboard Layout
    public array $keyboard_layout = [];
    public array $quick_replies = [];
    
    // Commands
    public array $commands = [];
    
    // Theme Settings
    public array $theme_settings = [];
    
    // Template Selection
    public ?TelegramBotTemplate $selectedTemplate = null;
    public array $availableTemplates = [];
    
    public bool $showTemplates = true;
    public bool $showBasicSettings = false;
    public bool $showPersonalitySettings = false;
    public bool $showKeyboardSettings = false;
    public bool $showCommandSettings = false;
    public bool $showThemeSettings = false;
    public bool $showPreview = false;
    
    public function mount(?int $botId = null)
    {
        $this->loadBot($botId);
        $this->loadTemplates();
        $this->initializeSettings();
    }
    
    protected function loadBot(?int $botId): void
    {
        if ($botId) {
            $this->bot = TelegramBot::where('business_id', Auth::user()->business_id)
                ->findOrFail($botId);
            $this->customization = $this->bot->customization;
        }
    }
    
    protected function loadTemplates(): void
    {
        $this->availableTemplates = TelegramBotTemplate::where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->get()
            ->toArray();
    }
    
    protected function initializeSettings(): void
    {
        if ($this->customization) {
            $this->bot_name = $this->customization->bot_name ?? '';
            $this->welcome_message = $this->customization->welcome_message ?? '';
            $this->help_message = $this->customization->help_message ?? '';
            $this->error_message = $this->customization->error_message ?? '';
            $this->language = $this->customization->language ?? 'en';
            
            $personality = $this->customization->personality_settings ?? [];
            $this->personality_tone = $personality['tone'] ?? 'professional';
            $this->personality_style = $personality['style'] ?? 'helpful';
            $this->personality_traits = $personality['traits'] ?? [];
            
            $this->keyboard_layout = $this->customization->keyboard_layout ?? [];
            $this->quick_replies = $this->customization->quick_replies ?? [];
            $this->commands = $this->customization->commands ?? [];
            $this->theme_settings = $this->customization->theme_settings ?? [];
        }
    }
    
    public function selectTemplate(int $templateId): void
    {
        $template = TelegramBotTemplate::findOrFail($templateId);
        $this->selectedTemplate = $template;
        
        // Apply template settings
        $templateData = $template->template_data;
        $this->bot_name = $templateData['bot_name'] ?? '';
        $this->welcome_message = $templateData['welcome_message'] ?? '';
        $this->help_message = $templateData['help_message'] ?? '';
        $this->personality_tone = $templateData['personality_tone'] ?? 'professional';
        $this->personality_style = $templateData['personality_style'] ?? 'helpful';
        $this->keyboard_layout = $templateData['keyboard_layout'] ?? [];
        $this->commands = $templateData['commands'] ?? [];
        
        $this->showTemplates = false;
        $this->showBasicSettings = true;
        
        // Increment template usage
        $template->incrementUsage();
    }
    
    public function addQuickReply(): void
    {
        $this->quick_replies[] = [
            'text' => '',
            'response' => '',
            'id' => uniqid(),
        ];
    }
    
    public function removeQuickReply(string $id): void
    {
        $this->quick_replies = array_filter($this->quick_replies, function ($reply) use ($id) {
            return $reply['id'] !== $id;
        });
    }
    
    public function addCommand(): void
    {
        $this->commands[] = [
            'command' => '',
            'description' => '',
            'response' => '',
            'id' => uniqid(),
        ];
    }
    
    public function removeCommand(string $id): void
    {
        $this->commands = array_filter($this->commands, function ($command) use ($id) {
            return $command['id'] !== $id;
        });
    }
    
    public function addKeyboardRow(): void
    {
        $this->keyboard_layout[] = [
            'buttons' => [],
            'id' => uniqid(),
        ];
    }
    
    public function removeKeyboardRow(string $id): void
    {
        $this->keyboard_layout = array_filter($this->keyboard_layout, function ($row) use ($id) {
            return $row['id'] !== $id;
        });
    }
    
    public function addKeyboardButton(string $rowId): void
    {
        foreach ($this->keyboard_layout as &$row) {
            if ($row['id'] === $rowId) {
                $row['buttons'][] = [
                    'text' => '',
                    'action' => 'text',
                    'value' => '',
                    'id' => uniqid(),
                ];
                break;
            }
        }
    }
    
    public function removeKeyboardButton(string $rowId, string $buttonId): void
    {
        foreach ($this->keyboard_layout as &$row) {
            if ($row['id'] === $rowId) {
                $row['buttons'] = array_filter($row['buttons'], function ($button) use ($buttonId) {
                    return $button['id'] !== $buttonId;
                });
                break;
            }
        }
    }
    
    public function saveCustomization(): void
    {
        $this->validate([
            'bot_name' => 'required|string|max:255',
            'welcome_message' => 'required|string|max:1000',
            'help_message' => 'required|string|max:1000',
            'error_message' => 'required|string|max:1000',
            'language' => 'required|string|size:2',
        ]);
        
        if (!$this->bot) {
            // Create new bot
            $this->bot = TelegramBot::create([
                'business_id' => Auth::user()->business_id,
                'bot_token' => '', // Will be set later
                'bot_username' => '',
                'is_active' => false,
            ]);
        }
        
        $customizationData = [
            'telegram_bot_id' => $this->bot->id,
            'bot_name' => $this->bot_name,
            'welcome_message' => $this->welcome_message,
            'help_message' => $this->help_message,
            'error_message' => $this->error_message,
            'language' => $this->language,
            'personality_settings' => [
                'tone' => $this->personality_tone,
                'style' => $this->personality_style,
                'traits' => $this->personality_traits,
            ],
            'keyboard_layout' => $this->keyboard_layout,
            'quick_replies' => $this->quick_replies,
            'commands' => $this->commands,
            'theme_settings' => $this->theme_settings,
            'is_active' => true,
        ];
        
        if ($this->customization) {
            $this->customization->update($customizationData);
        } else {
            $this->customization = TelegramBotCustomization::create($customizationData);
        }
        
        session()->flash('success', 'Bot customization saved successfully!');
    }
    
    public function toggleSection(string $section): void
    {
        $this->showTemplates = false;
        $this->showBasicSettings = false;
        $this->showPersonalitySettings = false;
        $this->showKeyboardSettings = false;
        $this->showCommandSettings = false;
        $this->showThemeSettings = false;
        $this->showPreview = false;
        
        $this->{"show{$section}"} = true;
    }
    
    public function getPersonalityTones(): array
    {
        return [
            'professional' => 'Professional',
            'friendly' => 'Friendly',
            'casual' => 'Casual',
            'formal' => 'Formal',
            'enthusiastic' => 'Enthusiastic',
            'calm' => 'Calm',
        ];
    }
    
    public function getPersonalityStyles(): array
    {
        return [
            'helpful' => 'Helpful',
            'informative' => 'Informative',
            'conversational' => 'Conversational',
            'direct' => 'Direct',
            'supportive' => 'Supportive',
            'educational' => 'Educational',
        ];
    }
    
    public function getAvailableLanguages(): array
    {
        return [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'ar' => 'Arabic',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
        ];
    }
    
    public function render()
    {
        return view('livewire.telegram-bot-builder');
    }
}