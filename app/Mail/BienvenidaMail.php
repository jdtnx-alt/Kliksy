<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombreUsuario;

    public string $urlApp;

    public function __construct(string $nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
        $this->urlApp = config('app.url');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a Kliksy! 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bienvenida',
        );
    }
}
