<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use App\Traits\CheckClientsProVerified;
use Livewire\Component;
use Livewire\WithPagination;

class SearchAnnouncement extends Component
{
    use WithPagination;
    use CheckClientsProVerified;

    public $title;  // Component parameter
    public $client_pro_verified = false;
    public $locations, $companies;
    public $search_title, $search_location_id;

    public function mount()
    {
        $this->client_pro_verified = $this->isClientProVerified();
        $this->locations = Location::select(['id', 'location_name'])->get();
    }

    public function searchAnnounces($title, $location_id)
    {
        $this->search_title = $title;
        $this->search_location_id = intval($location_id);
        $this->title = null;
    }

    public function clearSearch()
    {
        $this->search_title = null;
        $this->search_location_id = null;
        $this->title = null;
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
        if ($this->title) $this->search_title = $this->title;
        else $this->title = null;

        $random_profesions = Profesion::inRandomOrder()->limit(7)->get();

        $base_query = Announcement::where('expiration_time', '>=', now())
            ->orderBy('updated_at', 'DESC');

        $filter_query = (clone $base_query)
            ->when($this->search_title, function ($query) {
                $profesion_ids = Profesion::where('profesion_name', 'LIKE', '%' . $this->search_title . '%')->pluck('id');
                $query->where('announce_title', 'LIKE', '%' . $this->search_title . '%')
                    ->orWhereHas('profesions', fn($sub_query) => $sub_query->whereIn('profesion_id', $profesion_ids));
            })
            ->when($this->search_location_id, fn($query) => $query->whereHas('locations', fn($sub_query) => $sub_query
                ->where('location_id', $this->search_location_id)));

        $count_results = $filter_query->count();

        $announcements = $count_results > 0
            ? $filter_query->simplePaginate(10)
            : $base_query->simplePaginate(10);

        return view('livewire.web.search-announcement', [
            'announcements' => $announcements,
            'count_results' => $count_results,
            'search_title' => $this->search_title,
            'random_profesions' => $random_profesions
        ]);
    }
}
