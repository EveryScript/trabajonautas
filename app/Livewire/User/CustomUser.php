<?php

namespace App\Livewire\User;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CustomUser extends Component
{
    public $user;
    public $user_verified_payment;

    public function mount($id)
    {
        Carbon::setlocale('es');
        if ($id && User::find($id))
            $this->user = User::find($id);
    }
    public function formatDate($datetime)
    {
        return Carbon::parse($datetime)->translatedFormat('l d/M/Y H:m:s');
    }

    public function save()
    {
        $duration_days = $this->user->account->accountType->duration_days;
        $this->user->account()->update([
            'verified_payment' => $this->user_verified_payment,
            'days_left' => intval($duration_days)
        ]);
        $this->redirectRoute('user', navigate: true);
    }

    public function render()
    {
        return view('livewire.user.custom-user');
    }
}
