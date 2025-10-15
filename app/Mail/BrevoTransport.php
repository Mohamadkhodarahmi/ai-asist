<?php

namespace App\Mail;

use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailTo;
use Brevo\Client\Model\SendSmtpEmailSender;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Log;

class BrevoTransport extends AbstractTransport
{
    protected $apiKey;
    protected $senderEmail;
    protected $senderName;

    public function __construct(string $apiKey, string $senderEmail, string $senderName)
    {
        parent::__construct();
        $this->apiKey = $apiKey;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    protected function doSend(SentMessage $message): void
    {
        $originalMessage = MessageConverter::toEmail($message->getOriginalMessage());

        try {
            // Configure Brevo API
            $config = Configuration::getDefaultConfiguration();
            $config->setApiKey('api-key', $this->apiKey);

            $apiInstance = new TransactionalEmailsApi(null, $config);

            // Prepare email data
            $sendSmtpEmail = new SendSmtpEmail();
            
            // Set sender
            $sender = new SendSmtpEmailSender();
            $sender->setEmail($this->senderEmail);
            $sender->setName($this->senderName);
            $sendSmtpEmail->setSender($sender);

            // Set recipients
            $to = [];
            foreach ($originalMessage->getTo() as $address) {
                $recipient = new SendSmtpEmailTo();
                $recipient->setEmail($address->getAddress());
                // Set name only if it exists, otherwise use email
                $name = $address->getName() ?: $address->getAddress();
                $recipient->setName($name);
                $to[] = $recipient;
            }
            $sendSmtpEmail->setTo($to);

            // Set subject
            $sendSmtpEmail->setSubject($originalMessage->getSubject());

            // Set content
            $htmlContent = $originalMessage->getHtmlBody();
            if ($htmlContent) {
                $sendSmtpEmail->setHtmlContent($htmlContent);
            }

            $textContent = $originalMessage->getTextBody();
            if ($textContent) {
                $sendSmtpEmail->setTextContent($textContent);
            }

            // Send email
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            
            Log::info('Brevo email sent successfully', [
                'message_id' => $result->getMessageId(),
                'to' => array_map(fn($addr) => $addr->getAddress(), $originalMessage->getTo()),
                'subject' => $originalMessage->getSubject()
            ]);

        } catch (\Exception $e) {
            Log::error('Brevo email sending failed', [
                'error' => $e->getMessage(),
                'to' => array_map(fn($addr) => $addr->getAddress(), $originalMessage->getTo()),
                'subject' => $originalMessage->getSubject()
            ]);

            throw $e;
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
