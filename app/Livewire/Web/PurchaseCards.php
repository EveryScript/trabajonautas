<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Traits\AuthorizeClients;
use Livewire\Component;

class PurchaseCards extends Component
{
    use AuthorizeClients;

    public function render()
    {
        return view('livewire.web.purchase-cards', [
            'account_types' => AccountType::select('id', 'name', 'price', 'duration_days')->get(),
            'client' => $this->getAuthClientWithAccount()
        ]);
    }
}
