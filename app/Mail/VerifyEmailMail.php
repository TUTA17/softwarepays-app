<?php

namespace App\Mail;

use App\Modules\Theme\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $otp;

    public function __construct(User $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác minh địa chỉ email SoftwarePays',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.verify_email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
