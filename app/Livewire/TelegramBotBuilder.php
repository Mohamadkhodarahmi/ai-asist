<?php

namespace App\Livewire;

use App\Models\TelegramBot;
use App\Models\TelegramBotCustomization;
use App\Models\TelegramBotTemplate;
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

    // Loading states
    public bool $isLoading = false;

    public bool $isSaving = false;

    public bool $isLoadingTemplates = false;

    public string $loadingMessage = '';

    public function mount(?int $botId = null)
    {
        $this->isLoading = true;
        $this->loadingMessage = 'Loading bot configuration...';

        try {
            $this->loadBot($botId);
            $this->loadTemplates();
            $this->initializeSettings();
        } finally {
            $this->isLoading = false;
        }
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
            ->map(function ($template) {
                $data = $template->toArray();
                // Ensure id is always present
                $data['id'] = $template->id;

                return $data;
            })
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

            // Ensure all existing items have proper IDs
            $this->ensureItemIds();
        } else {
            // Initialize with default values for new bots
            $this->bot_name = '';
            $this->welcome_message = '';
            $this->help_message = '';
            $this->error_message = '';
            $this->language = 'en';

            // Initialize empty arrays with proper structure
            $this->keyboard_layout = [];
            $this->quick_replies = [];
            $this->commands = [];
            $this->theme_settings = [];
        }
    }

    private function ensureItemIds(): void
    {
        // Ensure keyboard layout rows have IDs and proper structure
        foreach ($this->keyboard_layout as &$row) {
            if (! isset($row['id']) || empty($row['id'])) {
                $row['id'] = uniqid();
            }

            // Ensure buttons array exists and has proper structure
            if (! isset($row['buttons'])) {
                $row['buttons'] = [];
            }

            // Ensure buttons have IDs
            foreach ($row['buttons'] as &$button) {
                if (! isset($button['id']) || empty($button['id'])) {
                    $button['id'] = uniqid();
                }

                // Ensure button has all required fields
                if (! isset($button['text'])) {
                    $button['text'] = '';
                }
                if (! isset($button['action'])) {
                    $button['action'] = 'text';
                }
                if (! isset($button['value'])) {
                    $button['value'] = '';
                }
            }
        }

        // Ensure quick replies have IDs and proper structure
        foreach ($this->quick_replies as &$reply) {
            if (! isset($reply['id']) || empty($reply['id'])) {
                $reply['id'] = uniqid();
            }

            // Ensure reply has all required fields
            if (! isset($reply['text'])) {
                $reply['text'] = '';
            }
            if (! isset($reply['response'])) {
                $reply['response'] = '';
            }
        }

        // Ensure commands have IDs and proper structure
        foreach ($this->commands as &$command) {
            if (! isset($command['id']) || empty($command['id'])) {
                $command['id'] = uniqid();
            }

            // Ensure command has all required fields
            if (! isset($command['command'])) {
                $command['command'] = '';
            }
            if (! isset($command['description'])) {
                $command['description'] = '';
            }
            if (! isset($command['response'])) {
                $command['response'] = '';
            }
        }
    }

    public function selectTemplate(int $templateId): void
    {
        $this->isLoading = true;
        $this->loadingMessage = 'Loading template...';

        try {
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

            session()->flash('success', 'Template loaded successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load template: '.$e->getMessage());
        } finally {
            $this->isLoading = false;
        }
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
        $this->quick_replies = array_values(array_filter($this->quick_replies, function ($reply) use ($id) {
            return ($reply['id'] ?? '') !== $id;
        }));
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
        $this->commands = array_values(array_filter($this->commands, function ($command) use ($id) {
            return ($command['id'] ?? '') !== $id;
        }));
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
        $this->keyboard_layout = array_values(array_filter($this->keyboard_layout, function ($row) use ($id) {
            return ($row['id'] ?? '') !== $id;
        }));
    }

    public function addKeyboardButton(string $rowId): void
    {
        foreach ($this->keyboard_layout as &$row) {
            if (($row['id'] ?? '') === $rowId) {
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
            if (($row['id'] ?? '') === $rowId) {
                if (isset($row['buttons'])) {
                    $row['buttons'] = array_values(array_filter($row['buttons'], function ($button) use ($buttonId) {
                        return ($button['id'] ?? '') !== $buttonId;
                    }));
                }
                break;
            }
        }
    }

    public function saveCustomization(): void
    {
        $this->isSaving = true;
        $this->loadingMessage = 'Saving bot configuration...';

        try {
            $this->validate([
                'bot_name' => 'required|string|max:255',
                'welcome_message' => 'required|string|max:1000',
                'help_message' => 'required|string|max:1000',
                'error_message' => 'required|string|max:1000',
                'language' => 'required|string|size:2',
            ]);

            if (! $this->bot) {
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
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save bot configuration: '.$e->getMessage());
        } finally {
            $this->isSaving = false;
        }
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

        // Ensure all items have proper IDs when navigating to any section
        $this->ensureItemIds();
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
