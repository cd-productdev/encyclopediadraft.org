<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $username;

    public string $ipAddress;

    public string $confirmationUrl;

    public string $invalidationUrl;

    public string $expiresAt;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $username,
        string $ipAddress,
        string $confirmationUrl,
        string $invalidationUrl,
        string $expiresAt
    ) {
        $this->username = $username;
        $this->ipAddress = $ipAddress;
        $this->confirmationUrl = $confirmationUrl;
        $this->invalidationUrl = $invalidationUrl;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email Confirmation - '.config('app.name', 'WikiEngine Bios & Wiki'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.email-confirmation',
            with: [
                'username' => $this->username,
                'ipAddress' => $this->ipAddress,
                'confirmationUrl' => $this->confirmationUrl,
                'invalidationUrl' => $this->invalidationUrl,
                'expiresAt' => $this->expiresAt,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
