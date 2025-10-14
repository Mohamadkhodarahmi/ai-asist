<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UsageLimitWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public array $usageData,
        public int $usagePercentage,
        public array $metrics = []
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->usagePercentage >= 100
            ? 'Usage Limit Reached - Action Required'
            : 'Approaching Usage Limit - '.$this->usagePercentage.'%';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.usage.limit-warning', [
                'user' => $notifiable,
                'usageData' => $this->usageData,
                'usagePercentage' => $this->usagePercentage,
                'metrics' => $this->metrics,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'usage_percentage' => $this->usagePercentage,
            'usage_data' => $this->usageData,
        ];
    }
}
