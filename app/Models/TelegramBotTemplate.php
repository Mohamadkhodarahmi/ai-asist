<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramBotTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'template_data',
        'preview_image',
        'price',
        'is_premium',
        'is_active',
        'usage_count',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'template_data' => 'array',
            'price' => 'decimal:2',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function getFormattedPrice(): string
    {
        if ($this->isFree()) {
            return 'Free';
        }
        
        return '$' . number_format($this->price, 2);
    }

    public function getCategoryIcon(): string
    {
        return match ($this->category) {
            'customer_service' => '🎧',
            'sales' => '💰',
            'education' => '📚',
            'healthcare' => '🏥',
            'ecommerce' => '🛒',
            'support' => '🆘',
            'marketing' => '📢',
            'general' => '🤖',
            default => '🤖',
        };
    }
}