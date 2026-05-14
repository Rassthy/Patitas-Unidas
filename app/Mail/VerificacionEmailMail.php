<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificacionEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $codigo,
        public string $nombre,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🐾 Verifica tu correo — PatitasUnidas',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verificacion-email',
        );
    }
}