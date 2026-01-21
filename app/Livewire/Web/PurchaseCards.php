<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\User;
use App\Traits\AuthorizeClients;
use Livewire\Component;

class PurchaseCards extends Component
{
    public function mount()
    {
        if (auth()->check())
            return $this->redirect('/panel', true);
    }

    public function render()
    {
        return view('livewire.web.purchase-cards', [
            'account_types' => AccountType::select('id', 'name', 'price', 'duration_days')->get()
        ]);
    }
}
