<?php

namespace App\Livewire\Announcement;

use App\Models\Announcement;
use App\Models\Company;
use App\Models\Location;
use App\Models\Profesion;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ListAnnouncement extends Component
{
    use WithPagination;

    public $search = null;

    public function delete($id)
    {
        Announcement::find($id)->delete();
    }

    #[Computed]
    public function totalLocations()
    {
        return Cache::remember('total_locations_count', 86400, fn() => Location::count());
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
    #[Computed]
    public function companies()
    {
        return Cache::remember('companies_list', 3600, fn() => Company::select('id', 'company_name')->orderBy('company_name')->get());
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Reset page when searching
    }

    public function render()
    {
        $query = Announcement::with(['profesions:id,profesion_name', 'locations:id,location_name', 'company:id,company_name'])
            ->select(['id', 'announce_title', 'updated_at', 'pro', 'scheduled_at', 'company_id', 'expiration_time'])
            ->orderBy('updated_at', 'DESC');

        if (!empty($this->search))
            $query->where('announce_title', 'LIKE', '%' . $this->search . '%')
                ->orWhereHas('profesions', fn($q) => $q->where('profesion_name', 'LIKE', '%' . $this->search . '%'))
                ->orWhereHas('company', fn($q) => $q->where('company_name', 'LIKE', '%' . $this->search . '%'))
                ->orWhereHas('locations', fn($q) => $q->where('location_name', 'LIKE', '%' . $this->search . '%'));

        $announcements = $query->simplePaginate(8);

        return view('livewire.announcement.list-announcement', [
            'announcements' => $announcements,
            'count_results' => $announcements->count(),
            'total_locations' => $this->totalLocations,
            'profesions' => $this->profesions,
            'locations' => $this->locations,
            'companies' => $this->companies
        ]);
    }
}
