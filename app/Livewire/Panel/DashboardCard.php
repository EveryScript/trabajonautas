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
    public $client_profesion_area_id = null;

    protected function baseQuery()
    {
        $query = Announcement::where('expiration_time', '>', now())->with(['company', 'locations'])
            ->whereHas('locations', fn($sub) => $sub->where('location_id', $this->client_location_id))
            ->orWhereHas('profesions', fn($sub) => $sub->where('profesion_id', $this->client_profesion_id))
            ->orWhereHas('profesions', fn($sub) => $sub->where('area_id', $this->client_profesion_area_id));
        return $query->orderBy('updated_at', 'DESC')->limit(7);
    }

    public function render()
    {
        return view('livewire.panel.dashboard-card', [
            'announces' => $this->my_announces_mode ? Auth::user()->myAnnounces : $this->baseQuery()->get(),
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent()
        ]);
    }
}
