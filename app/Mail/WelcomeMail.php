<?php

namespace App\Mail;

use App\Modules\Theme\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chào mừng bạn đến với SoftwarePays!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.welcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
