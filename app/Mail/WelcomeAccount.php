<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeAccount extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $account_type_name;
    public $account_type_details;

    public function __construct($user, $account_type_name)
    {
        $this->user = $user;
        $this->account_type_name = strtoupper($account_type_name);

        $this->account_type_details = match ($this->account_type_name) {
            'PRO' => [
                'color' => '#ff420a',
                'label' => 'Cuenta PRO',
                'feature' => 'Si ya realizaste tu depósito por tu cuenta PRO, te avisaremos cuando se haya aprobado tu solicitud.'
            ],
            'PRO-MAX' => [
                'color' => '#ff420a',
                'label' => 'Cuenta PRO-MAX',
                'feature' => 'Si ya realizaste tu depósito por tu cuenta PRO-MAX, te avisaremos cuando se haya aprobado tu solicitud.'
            ],
            default => [
                'color' => '#16a34a',
                'label' => 'Cuenta FREE',
                'feature' => 'Recuerda que para tener una mejor experiencia estás invitado/a a adquirir una cuenta PRO-MAX.'
            ]
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "¡Bienvenido a Trabajonautas {$this->account_type_name}!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-account',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
