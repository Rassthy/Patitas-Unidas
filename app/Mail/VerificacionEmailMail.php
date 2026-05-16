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

    public $codigo;
    public $nombre;
    public $userId;

    public function __construct($codigo, $nombre, $userId)
    {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->userId = $userId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verificación de Correo - PatitasUnidas',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verificacion-email',
        );
    }
}