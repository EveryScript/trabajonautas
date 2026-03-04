<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\Profesion;
use App\Traits\AuthorizeClients;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchAnnouncement extends Component
{
    use AuthorizeClients;

    public $profesion_id, $location_id;

    protected function announceBaseQuery()
    {
        return Announcement::query()->where('expiration_time', '>=', now())
            // Filter by profesion
            ->when($this->profesion_id, function ($query, $profesion_id) {
                $query->whereHas('profesions', function ($q) use ($profesion_id) {
                    $q->where('profesions.id', $profesion_id);
                });
            })
            // Filter by location
            ->when($this->location_id, function ($query, $location_id) {
                $query->whereHas('locations', function ($q) use ($location_id) {
                    $q->where('locations.id', $location_id);
                });
            });
    }

    #[Computed]
    public function announcements()
    {
        return $this->announceBaseQuery()
            ->select('id', 'announce_title', 'company_id', 'pro', 'expiration_time', 'created_at', 'updated_at')
            ->with([
                'company:id,company_name,company_image',
                'locations:id,location_name'
            ])
            ->latest('updated_at')
            ->simplePaginate(12);
    }

    #[Computed]
    public function hasResults()
    {
        return $this->announcements->isNotEmpty();
    }

    #[Computed]
    public function recommends()
    {
        if (!$this->profesion_id) return collect();

        $profesion = Profesion::find($this->profesion_id);
        if (!$profesion) return collect();

        return Announcement::query()
            ->select('id', 'announce_title', 'company_id', 'pro', 'expiration_time') // Solo lo necesario
            ->with(['company:id,company_name,company_image', 'locations:id,location_name'])
            ->where('expiration_time', '>=', now())
            ->whereHas('profesions', fn($q) => $q->where('area_id', $profesion->area_id))
            ->whereNotIn('id', $this->announcements->pluck('id')->toArray()) // Evitar duplicados
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function totalResults()
    {
        return $this->announceBaseQuery()->count();
    }

    #[Computed]
    public function profesions()
    {
        return Cache::remember('profesions_list', 3600, fn() => Profesion::select('id', 'profesion_name')->orderBy('profesion_name')->get());
    }

    #[Computed]
    public function locations()
    {
        return Cache::remember('locations_list', 3600, fn() => Location::select('id', 'location_name')->orderBy('location_name')->get());
    }

    public function render()
    {
        return view('livewire.web.search-announcement', [
            'announcements' => $this->announcements,
            'recommends' => $this->recommends,
            'hasResults' => $this->hasResults,
            'profesions' => $this->profesions,
            'locations' => $this->profesions,
            'locations' => $this->locations,
            'client_pro_authorized' => $this->isAuthClientProVerifiedAndCurrent()
        ]);
    }
}
