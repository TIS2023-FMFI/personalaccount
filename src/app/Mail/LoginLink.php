<?php

namespace App\Mail;

use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * An email containing a login link.
 */
class LoginLink extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The login link.
     * 
     * @var string
     */
    private $url;

    /**
     * The date and time until which the link is valid.
     * 
     * @var DateTimeInterface
     */
    private $validUntil;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $token, DateTimeInterface $validUntil)
    {
        $this->validUntil = $validUntil;
        $this->url = URL::temporarySignedRoute(
            'login-using-token',
            $validUntil,
            [ 'token' => $token ]
        );
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: trans(
                'emails.login-link.subject',
                [ 'appName' => config('app.name') ]
            ),
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
                'validUntil' => $this->validUntil,
                'url' => $this->url,
            ]
        );
    }
}
