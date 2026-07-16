<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewDeviceAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ip;
    public $userAgent;
    public $time;

    /**
     * Create a new message instance.
     */
    public function __construct($ip, $userAgent, $time)
    {
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->time = $time;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cảnh báo: Đăng nhập từ thiết bị lạ - SoftwarePays',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.new_device',
            with: [
                'ip' => $this->ip,
                'userAgent' => $this->userAgent,
                'time' => $this->time,
            ]
        );
    }
}
