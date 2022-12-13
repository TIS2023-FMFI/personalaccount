<?php

namespace App\Mail;

use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class LoginLink extends Mailable
{
    use Queueable, SerializesModels;

    private $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $token, DateTimeInterface $validUntil)
    {
        $this->url = URL::temporarySignedRoute('login-using-token', $validUntil, [
            'token' => $token,
        ]);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: trans('emails.login-link-subject'),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.login_link',
            with: [
                'url' => $this->url,
            ]
        );
    }
}
