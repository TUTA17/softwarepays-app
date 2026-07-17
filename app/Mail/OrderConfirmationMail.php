<?php

namespace App\Mail;

use App\Modules\Theme\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $items;
    public float $total;

    /**
     * @param array<int, array{name: string, price: float}> $items
     */
    public function __construct(User $user, array $items, float $total)
    {
        $this->user = $user;
        $this->items = $items;
        $this->total = $total;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đơn hàng - SoftwarePays',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shop.order_confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
