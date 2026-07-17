<?php

namespace App\Mail;

use App\Modules\Theme\Models\Transaction;
use App\Modules\Theme\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Transaction $transaction;

    public function __construct(User $user, Transaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận giao dịch nạp tiền - SoftwarePays',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.wallet.transaction_confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
