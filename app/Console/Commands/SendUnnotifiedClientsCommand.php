<?php

namespace App\Console\Commands;

use App\Livewire\Announcement\FormAnnouncement;
use Illuminate\Console\Command;

class SendUnnotifiedClientsCommand extends Command
{
    protected $signature = 'trabajonautas:send-unnotified-clients';

    protected $description = 'Envía notificaiones a los clientes no notificados durante el día.';

    public function handle()
    {
        $component = new FormAnnouncement();
        // $component->sendUnnotifiedClients();
        $component->sendToAllClients();
        $this->info('Notificaciones enviadas a usuarios no notificados');
    }
}
