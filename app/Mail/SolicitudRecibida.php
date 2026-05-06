<?php

namespace App\Mail;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudRecibida extends Mailable
{
    use Queueable, SerializesModels;

    public Solicitud $solicitud;

    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📩 Nueva solicitud de servicio — Kliksy',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-recibida',
        );
    }
}
