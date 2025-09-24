<?php

namespace App\Jobs;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SetTelegramWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Business $business;

    /**
     * Create a new job instance.
     */
    public function __construct(Business $business)
    {
        $this->business = $business;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (! $this->business->telegram_token) {
                Log::warning("Business {$this->business->id} has no Telegram token");

                return;
            }

            $webhookUrl = route('telegram.bot', ['token' => $this->business->telegram_token]);

            $response = Http::post("https://api.telegram.org/bot{$this->business->telegram_token}/setWebhook", [
                'url' => $webhookUrl,
            ]);

            if ($response->successful() && $response->json('ok') === true) {
                Log::info("Webhook set successfully for business {$this->business->id} ({$this->business->name})");
            } else {
                Log::error("Failed to set webhook for business {$this->business->id}: ".$response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception while setting webhook for business {$this->business->id}: ".$e->getMessage());
        }
    }
}
