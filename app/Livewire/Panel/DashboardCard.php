<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Traits\AuthorizeClients;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardCard extends Component
{
    use AuthorizeClients;
    // Parameters
    public $title, $description;
    public $my_announces_mode = false;
    public $client_account_id = null;
    public $client_location_id = null;
    public $client_profesion_id = null;

    protected function baseQuery()
    {
        $query = Announcement::with(['company', 'locations'])->where('expiration_time', '>=', now());

        if (intval($this->client_account_id) === 1) {
            // Free Suggests: same location
            $query->whereHas('locations', fn($q) => $q->where('location_id', $this->client_location_id));
        } else {
            // PRO or PRO-MAX Suggests: same locations or profesion
            $query->whereHas('locations', fn($sub) => $sub->where('location_id', $this->client_location_id))
                ->orWhereHas('profesions', fn($sub) => $sub->where('profesion_id', $this->client_profesion_id));
        }
        return $query->orderBy('updated_at', 'DESC');
    }

    public function render()
    {
        return view('livewire.panel.dashboard-card', [
            'announces' => $this->my_announces_mode ? Auth::user()->myAnnounces : $this->baseQuery()->get(),
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent()
        ]);
    }
}
