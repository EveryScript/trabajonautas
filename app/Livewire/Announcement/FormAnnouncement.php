<?php

namespace App\Livewire\Announcement;

use App\Livewire\Forms\AnnouncementForm;
use App\Models\Announcement;
use App\Models\Area;
use App\Models\Company;
use App\Models\Location;
use App\Models\Profesion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormAnnouncement extends Component
{
    use WithFileUploads;

    public $id; // Edit
    public AnnouncementForm $announcement;
    public $areas, $locations, $companies, $profesions;

    public function mount($id = null)
    {
        if ($id && Announcement::find($id)) {
            $this->id = $id;
            $this->announcement->edit($id);
        }
    }

    public function save()
    {
        $this->announcement->user_id = Auth::id();
        $this->announcement->save();
        $this->redirectRoute('announcement', navigate: true);
    }

    public function update()
    {
        // $clients = User::role(env('CLIENT_ROLE'))
        //     ->whereHas('account', fn($query) => $query
        //         ->where('account_type_id', 3))
        //     ->whereHas('location', fn($query) => $query
        //         ->whereIn('location_id', $this->announcement->locations))
        //     ->whereHas('myProfesions', fn($query)  => $query
        //         ->whereIn('profesion_id', $this->announcement->profesions))
        //     ->get();
        // dump($clients);
        $this->announcement->update($this->id);
        $this->redirectRoute('announcement', navigate: true);
    }

    public function render()
    {
        $this->areas = Area::all(['id', 'area_name']);
        $this->locations = Location::all(['id', 'location_name']);
        $this->companies = Company::all(['id', 'company_name']);
        $this->profesions = Profesion::all(['id', 'profesion_name']);
        return view('livewire.announcement.form-announcement');
    }
}
