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
            $this->client = User::with('account.accountType')->find($id);
            $this->client_verified_payment = $this->client->account ? $this->client->account->verified_payment : 'none';
            $this->client_actived = $this->client->actived;
        }
    }

    public function save()
    {
        $duration_days = $this->client->account->accountType->duration_days;
        $this->client->account()->update([
            'verified_payment' => $this->client_verified_payment,
            'limit_time' => Carbon::now()->addDays($duration_days),
            'verified_by_user_id' => auth()->user()->id
        ]);
        $this->client->update([
            'actived' => $this->client_actived
        ]);
    }

    public function formatDate($datetime)
    {
        return Carbon::parse($datetime)->translatedFormat('l d/M/Y H:m:s');
    }

    public function justExit()
    {
        $this->redirectRoute('client', navigate: true);
    }

    public function render()
    {
        return view('livewire.user.config-client');
    }
}
