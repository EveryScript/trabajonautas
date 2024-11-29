<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Models\Company;
use App\Models\Location;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchAnnouncement extends Component
{
    use WithPagination;
    #[Url]
    public $title;
    public $locations, $companies;
    public $search_title, $search_location;
    public $announce_area;

    public function mount($title = null)
    {
        if ($title) {
            $this->search_title = $this->title;
        }
        $this->locations = Location::select(['id', 'location_name'])->get();
    }

    public function searchAnnounces($title, $location_id)
    {
        $this->search_title = $title;
        $this->search_location = $location_id;
    }

    public function render()
    {
        sleep(0.8); // Delay or loading
        $announcements = Announcement::orderBy('updated_at', 'DESC')
            ->when($this->search_title, function ($query) {
                $query->where('announce_title', 'LIKE', '%' . $this->search_title . '%');
            })
            ->when($this->search_location, function ($query) {
                $query->whereHas('locations', function ($sub_query) {
                    $sub_query->where('location_id', $this->search_location);
                });
            });
        return view('livewire.web.search-announcement', [
            'announcements' => $announcements->simplePaginate(12)
        ]);
    }
}
