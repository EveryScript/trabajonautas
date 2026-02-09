<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewAccount extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $client;
    public $account_type_name;
    public $account_type_details;

    public function __construct($client, $account_type_name)
    {
        $this->client = $client;
        $this->account_type_name = strtoupper($account_type_name);

        $this->account_type_details = match ($this->account_type_name) {
            'PRO' => [
                'color' => '#ff420a',
                'label' => 'Cuenta PRO',
                'feature' => 'Tienes acceso a todas las convocatorias PRO correspondientes a tu profesión.'
            ],
            'PRO-MAX' => [
                'color' => '#ff420a',
                'label' => 'Cuenta PRO-MAX',
                'feature' => 'Tienes acceso a todas las convocatorias PRO y notificaciones personalizadas.'
            ],
            default => [
                'color' => '#16a34a',
                'label' => 'Cuenta FREE',
                'feature' => 'Tienes acceso a las funciones básicas para empezar tu búsqueda.'
            ]
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
           subject: "¡Cuenta actualizada - Trabajonautas {$this->account_type_name}!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.renew-account',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
