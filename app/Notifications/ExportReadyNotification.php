<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $format,
        public string $downloadUrl,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?int $recordCount = null,
        public ?string $fileSize = null
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
        return (new MailMessage)
            ->subject('Your '.strtoupper($this->format).' Export is Ready!')
            ->view('emails.export.export-ready', [
                'user' => $notifiable,
                'format' => $this->format,
                'downloadUrl' => $this->downloadUrl,
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
                'recordCount' => $this->recordCount,
                'fileSize' => $this->fileSize,
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
            'format' => $this->format,
            'download_url' => $this->downloadUrl,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];
    }
}
