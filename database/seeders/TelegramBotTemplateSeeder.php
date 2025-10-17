<?php

namespace Database\Seeders;

use App\Models\TelegramBotTemplate;
use Illuminate\Database\Seeder;

class TelegramBotTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Customer Service Bot',
                'description' => 'Professional customer service bot for handling inquiries and support requests',
                'category' => 'customer_service',
                'template_data' => [
                    'bot_name' => 'Customer Service Assistant',
                    'welcome_message' => 'Hello! I\'m your customer service assistant. How can I help you today?',
                    'help_message' => 'I can help you with:\n• Product information\n• Order status\n• Returns and exchanges\n• Technical support\n\nJust ask me anything!',
                    'personality_tone' => 'professional',
                    'personality_style' => 'helpful',
                    'keyboard_layout' => [
                        [
                            'buttons' => [
                                ['text' => 'Order Status', 'action' => 'text', 'value' => 'Check my order status'],
                                ['text' => 'Returns', 'action' => 'text', 'value' => 'I want to return an item'],
                            ]
                        ],
                        [
                            'buttons' => [
                                ['text' => 'Contact Support', 'action' => 'text', 'value' => 'I need to speak with a human'],
                                ['text' => 'FAQ', 'action' => 'text', 'value' => 'Show me frequently asked questions'],
                            ]
                        ]
                    ],
                    'commands' => [
                        ['command' => '/order', 'description' => 'Check order status', 'response' => 'Please provide your order number'],
                        ['command' => '/support', 'description' => 'Get support', 'response' => 'I\'m here to help! What\'s the issue?'],
                    ]
                ],
                'price' => 0,
                'is_premium' => false,
                'is_active' => true,
                'usage_count' => 150,
            ],
            [
                'name' => 'Sales Assistant Bot',
                'description' => 'Enthusiastic sales bot for product recommendations and lead generation',
                'category' => 'sales',
                'template_data' => [
                    'bot_name' => 'Sales Assistant',
                    'welcome_message' => 'Hi there! 👋 I\'m here to help you find the perfect product. What are you looking for today?',
                    'help_message' => 'I can help you:\n• Find products\n• Compare options\n• Get recommendations\n• Check availability\n• Process orders\n\nLet\'s find something amazing for you!',
                    'personality_tone' => 'enthusiastic',
                    'personality_style' => 'conversational',
                    'keyboard_layout' => [
                        [
                            'buttons' => [
                                ['text' => 'Browse Products', 'action' => 'text', 'value' => 'Show me your products'],
                                ['text' => 'Get Recommendations', 'action' => 'text', 'value' => 'Recommend something for me'],
                            ]
                        ],
                        [
                            'buttons' => [
                                ['text' => 'Special Offers', 'action' => 'text', 'value' => 'Show me current deals'],
                                ['text' => 'Contact Sales', 'action' => 'text', 'value' => 'I want to speak with sales'],
                            ]
                        ]
                    ],
                    'commands' => [
                        ['command' => '/products', 'description' => 'Browse products', 'response' => 'Here are our featured products!'],
                        ['command' => '/deals', 'description' => 'View special offers', 'response' => 'Check out these amazing deals!'],
                    ]
                ],
                'price' => 9.99,
                'is_premium' => true,
                'is_active' => true,
                'usage_count' => 89,
            ],
            [
                'name' => 'Educational Tutor Bot',
                'description' => 'Patient educational bot for learning and knowledge sharing',
                'category' => 'education',
                'template_data' => [
                    'bot_name' => 'Learning Assistant',
                    'welcome_message' => 'Hello! I\'m your learning assistant. I\'m here to help you understand and learn new concepts. What would you like to explore today?',
                    'help_message' => 'I can help you with:\n• Explaining concepts\n• Answering questions\n• Providing examples\n• Practice exercises\n• Study guidance\n\nLearning is a journey - let\'s start together!',
                    'personality_tone' => 'calm',
                    'personality_style' => 'educational',
                    'keyboard_layout' => [
                        [
                            'buttons' => [
                                ['text' => 'Ask Question', 'action' => 'text', 'value' => 'I have a question'],
                                ['text' => 'Practice Quiz', 'action' => 'text', 'value' => 'Give me a practice question'],
                            ]
                        ],
                        [
                            'buttons' => [
                                ['text' => 'Study Tips', 'action' => 'text', 'value' => 'Give me study tips'],
                                ['text' => 'Progress Check', 'action' => 'text', 'value' => 'Check my progress'],
                            ]
                        ]
                    ],
                    'commands' => [
                        ['command' => '/quiz', 'description' => 'Start a quiz', 'response' => 'Let\'s test your knowledge!'],
                        ['command' => '/tips', 'description' => 'Get study tips', 'response' => 'Here are some helpful study tips!'],
                    ]
                ],
                'price' => 0,
                'is_premium' => false,
                'is_active' => true,
                'usage_count' => 67,
            ],
            [
                'name' => 'Healthcare Assistant Bot',
                'description' => 'Professional healthcare bot for medical information and appointment scheduling',
                'category' => 'healthcare',
                'template_data' => [
                    'bot_name' => 'Healthcare Assistant',
                    'welcome_message' => 'Hello! I\'m your healthcare assistant. I can help you with general health information and appointment scheduling. How can I assist you today?',
                    'help_message' => 'I can help you with:\n• General health information\n• Appointment scheduling\n• Medication reminders\n• Health tips\n• Finding specialists\n\nNote: This is not a substitute for professional medical advice.',
                    'personality_tone' => 'professional',
                    'personality_style' => 'supportive',
                    'keyboard_layout' => [
                        [
                            'buttons' => [
                                ['text' => 'Book Appointment', 'action' => 'text', 'value' => 'I want to book an appointment'],
                                ['text' => 'Health Info', 'action' => 'text', 'value' => 'I need health information'],
                            ]
                        ],
                        [
                            'buttons' => [
                                ['text' => 'Medication Reminder', 'action' => 'text', 'value' => 'Set medication reminder'],
                                ['text' => 'Emergency', 'action' => 'text', 'value' => 'This is an emergency'],
                            ]
                        ]
                    ],
                    'commands' => [
                        ['command' => '/appointment', 'description' => 'Book appointment', 'response' => 'I\'ll help you schedule an appointment'],
                        ['command' => '/reminder', 'description' => 'Set medication reminder', 'response' => 'Let\'s set up your medication reminder'],
                    ]
                ],
                'price' => 19.99,
                'is_premium' => true,
                'is_active' => true,
                'usage_count' => 34,
            ],
            [
                'name' => 'E-commerce Helper Bot',
                'description' => 'Friendly e-commerce bot for shopping assistance and order management',
                'category' => 'ecommerce',
                'template_data' => [
                    'bot_name' => 'Shopping Assistant',
                    'welcome_message' => 'Welcome to our store! 🛍️ I\'m here to help you find great products and manage your orders. What can I help you with today?',
                    'help_message' => 'I can help you:\n• Find products\n• Check order status\n• Process returns\n• Track shipments\n• Get product recommendations\n• Answer questions about items\n\nHappy shopping!',
                    'personality_tone' => 'friendly',
                    'personality_style' => 'helpful',
                    'keyboard_layout' => [
                        [
                            'buttons' => [
                                ['text' => 'Shop Now', 'action' => 'text', 'value' => 'Show me products'],
                                ['text' => 'My Orders', 'action' => 'text', 'value' => 'Check my orders'],
                            ]
                        ],
                        [
                            'buttons' => [
                                ['text' => 'Track Package', 'action' => 'text', 'value' => 'Track my package'],
                                ['text' => 'Returns', 'action' => 'text', 'value' => 'Start a return'],
                            ]
                        ]
                    ],
                    'commands' => [
                        ['command' => '/shop', 'description' => 'Browse products', 'response' => 'Let\'s find something you\'ll love!'],
                        ['command' => '/track', 'description' => 'Track order', 'response' => 'Please provide your order number'],
                    ]
                ],
                'price' => 0,
                'is_premium' => false,
                'is_active' => true,
                'usage_count' => 112,
            ],
        ];

        foreach ($templates as $template) {
            TelegramBotTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}