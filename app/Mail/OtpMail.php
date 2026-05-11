<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public int $otp;  // ✅ OTP store hoga

    public function __construct(int $otp)
    {
        $this->otp = $otp;  // ✅ OTP receive karega
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your OTP — Arklen Agro',  // ✅ Subject
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}