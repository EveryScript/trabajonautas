<?php

namespace App\Livewire\Panel;

use App\Models\Announcement;
use App\Traits\CheckClientsProVerified;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardCard extends Component
{
    use CheckClientsProVerified;
    // Parameters
    public $client_account_id = null;
    public $client_location_id = null;
    public $client_profesion_ids = [];
    // Propeties
    public $suggests = [];
    public $filter_title = "Filtrar";

    public function mount()
    {
        $this->loadSuggests();
    }

    protected function baseQuery()
    {
        $query = Announcement::where('expiration_time', '>=', now());

        if ($this->client_account_id === 1) {
            // Free Suggests
            $query->whereHas('locations', fn($q) => $q->where('location_id', $this->client_location_id));
        } else {
            // PRO or PRO-MAX Suggests
            $query->where(function ($sub_query) {
                $sub_query->whereHas('profesions', fn($sub) => $sub->whereIn('profesion_id', $this->client_profesion_ids ?? []))
                    ->orWhereHas('locations', fn($sub) => $sub->where('location_id', $this->client_location_id));
            });
        }

        return $query;
    }

    protected function loadSuggests()
    {
        $this->suggests = $this->baseQuery()->orderBy('updated_at', 'DESC')->get();
    }

    public function filterSuggests($option)
    {
        switch ($option) {
            case 1: // Filter today
                $this->filter_title = "Publicados hoy";
                $this->suggests = $this->baseQuery()
                    ->whereDate('created_at', Carbon::today())
                    ->orderBy('updated_at', 'DESC')
                    ->get();
                break;

            case 2: // Filter this week
                $this->filter_title = "Publicados esta semana";
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $this->suggests = $this->baseQuery()
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->get();
                break;

            case 3: // Filter this month
                $this->filter_title = "Publicados este mes";
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $this->suggests = $this->baseQuery()
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('updated_at', 'DESC')
                    ->get();
                break;
            case 4: // saved by current user
                $this->filter_title = "Mis convocatoias guardadas";
                $this->suggests = Auth::user()->myAnnounces;
                break;

            default:
                $this->loadSuggests();
                break;
        }
    }

    public function isAnnouncePro($pro)
    {
        if ($pro) {
            if ($this->isClientRole())
                return $this->isClientProVerified() ? true : false;
            else
                return true;
        } else
            return true;
    }

    public function render()
    {
        return view('livewire.panel.dashboard-card');
    }
}
