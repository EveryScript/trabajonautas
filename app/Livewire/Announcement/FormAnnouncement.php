<?php

namespace App\Livewire\Announcement;

use App\Jobs\SendAnnouncementNotifications;
use App\Livewire\Forms\AnnouncementForm;
use App\Models\Announcement;
use App\Models\AnnouncementFile;
use App\Models\Area;
use App\Models\Company;
use App\Models\Location;
use App\Models\Profesion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormAnnouncement extends Component
{
    use WithFileUploads;

    public $id; // Edit
    public AnnouncementForm $announcement;

    public function mount($id = null)
    {
        if ($id && Announcement::find($id)) {
            $this->id = $id;
            $this->announcement->edit($id);
        }
    }

    public function save()
    {
        $this->announcement->user_id = auth()->user()->id;
        $announce_saved = $this->announcement->save();
        if ($this->announcement->pro && !$this->announcement->notification_sent) {
            SendAnnouncementNotifications::dispatch($announce_saved);
        }
        $this->redirectRoute('announcement', navigate: true);
    }

    public function update()
    {
        $this->announcement->update($this->id);
        $announce_updated = Announcement::find($this->id);
        if ($this->announcement->pro && !$this->announcement->notification_sent) {
            SendAnnouncementNotifications::dispatch($announce_updated);
        }
        $this->redirectRoute('announcement', navigate: true);
    }

    public function deleteCurrentFile($file_id)
    {
        $announce_file = AnnouncementFile::find($file_id);
        if ($announce_file) {
            // Delete file from storage
            if (Storage::disk('public')->exists($announce_file->url)) {
                Storage::disk('public')->delete($announce_file->url);
            }
            $announce_file->delete();
            $this->announcement->current_files = $this->announcement->current_files->where('id', '!=', $file_id);
        }
    }

    #[Computed]
    public function areas()
    {
        return Cache::remember('areas_list', 86400, fn() => Area::all(['id', 'area_name']));
    }

    #[Computed]
    public function locations()
    {
        return Cache::remember('locations_list', 86400, fn() => Location::all(['id', 'location_name']));
    }

    #[Computed]
    public function companies()
    {
        return Cache::remember('companies_list', 86400, fn() => Company::all(['id', 'company_name']));
    }

    #[Computed]
    public function profesions()
    {
        return Cache::remember('profesions_list', 86400, fn() => Profesion::with('area:id,area_name')->get(['id', 'profesion_name', 'area_id']));
    }

    public function render()
    {
        return view('livewire.announcement.form-announcement', [
            'areas' => $this->areas,
            'locations' => $this->locations,
            'companies' => $this->companies,
            'profesions' => $this->profesions,
        ]);
    }
}
