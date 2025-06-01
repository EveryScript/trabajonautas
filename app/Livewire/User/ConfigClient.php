<?php

namespace App\Livewire\User;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class ConfigClient extends Component
{
    public $client;
    public $client_verified_payment;
    public $client_actived;

    public function mount($id)
    {
        Carbon::setlocale('es');
        if ($id && User::find($id)) {
            $this->client = User::whereHas('account')
                ->with('account.accountType')
                ->find($id);
            $this->client_verified_payment = $this->client->account->verified_payment;
            $this->client_actived = $this->client->actived;
        }
    }

    public function save()
    {
        $duration_days = $this->client->account->accountType->duration_days;
        $this->client->account()->update([
            'verified_payment' => $this->client_verified_payment,
            'limit_time' => Carbon::now()->addDays($duration_days)
        ]);
        $this->client->update([
            'actived' => $this->client_actived
        ]);
        $this->redirectRoute('client', navigate: true);
    }

    public function formatDate($datetime)
    {
        return Carbon::parse($datetime)->translatedFormat('l d/M/Y H:m:s');
    }

    public function render()
    {
        return view('livewire.user.config-client');
    }
}
