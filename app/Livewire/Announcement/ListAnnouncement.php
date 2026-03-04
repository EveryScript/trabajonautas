<?php

namespace App\Livewire\Announcement;

use App\Models\Announcement;
use App\Models\Location;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ListAnnouncement extends Component
{
    use WithPagination;

    public $search;

    public function delete($id)
    {
        Announcement::find($id)->delete();
    }

    #[Computed]
    public function totalLocations()
    {
        return Cache::remember('total_locations_count', 86400, fn() => Location::count());
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Reset page when searching
    }

    public function render()
    {
        $query = Announcement::with(['profesions:id,profesion_name', 'area:id,area_name'])
            ->select(['id', 'announce_title', 'updated_at', 'pro', 'company_id', 'area_id', 'expiration_time'])
            ->orderBy('updated_at', 'DESC');

        if (!empty($this->search))
            $query->where('announce_title', 'LIKE', '%' . $this->search . '%');

        $announcements = $query->simplePaginate(8);

        return view('livewire.announcement.list-announcement', [
            'announcements' => $announcements,
            'count_results' => $announcements->count(),
            'total_locations' => $this->totalLocations
        ]);
    }
}
