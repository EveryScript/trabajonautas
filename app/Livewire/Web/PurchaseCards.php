<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\TbnSetting;
use App\Traits\AuthorizeClients;
use Livewire\Component;

class PurchaseCards extends Component
{
    use AuthorizeClients;

    public function render()
    {
        return view('livewire.web.purchase-cards', [
            'account_types' => AccountType::select('id', 'name', 'price', 'duration_days')->get(),
            'tbn_coins' => TbnSetting::where('key', 'tbn_coins')->value('value'),
            'client' => $this->getAuthClientWithAccount()
        ]);
    }
}
