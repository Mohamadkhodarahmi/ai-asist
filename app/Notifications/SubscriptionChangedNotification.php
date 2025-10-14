<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $oldPlan,
        public string $newPlan,
        public string $type = 'changed',
        public array $features = []
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
        $subject = match ($this->type) {
            'upgrade' => 'Your Plan Has Been Upgraded! 🚀',
            'downgrade' => 'Your Plan Has Been Changed',
            default => 'Your Subscription Has Been Updated',
        };

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.subscription.plan-changed', [
                'user' => $notifiable,
                'oldPlan' => $this->oldPlan,
                'newPlan' => $this->newPlan,
                'type' => $this->type,
                'features' => $this->features,
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
            'old_plan' => $this->oldPlan,
            'new_plan' => $this->newPlan,
            'type' => $this->type,
        ];
    }
}
