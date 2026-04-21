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
use Carbon\Carbon;
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
            if ($this->announcement->scheduled_at) {
                // Scheduled notification
                $delay = Carbon::parse($this->announcement->scheduled_at);
                SendAnnouncementNotifications::dispatch($announce_saved)->delay($delay);
            } else {
                // Inmediately
                SendAnnouncementNotifications::dispatch($announce_saved);
            }
        }
        $this->forgetCacheLists();
        $this->redirectRoute('announcement', navigate: true);
    }

    public function update()
    {
        $this->announcement->update($this->id);
        $announce_updated = Announcement::find($this->id);
        if ($this->announcement->pro && !$this->announcement->notification_sent) {
            if ($this->announcement->scheduled_at) {
                // Scheduled notification
                $delay = Carbon::parse($this->announcement->scheduled_at);
                SendAnnouncementNotifications::dispatch($announce_updated)->delay($delay);
            } else {
                // Inmediately
                SendAnnouncementNotifications::dispatch($announce_updated);
            }
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
    public function profesions()
    {
        return Cache::remember('profesions-v1', 86400, fn() => Profesion::all(['id', 'profesion_name', 'area_id']));
    }

    #[Computed]
    public function locations()
    {
        return Cache::remember('locations-v1', 86400, fn() => Location::all(['id', 'location_name']));
    }

    #[Computed]
    public function areas()
    {
        return Cache::remember('areas-v1', 86400, fn() => Area::all(['id', 'area_name']));
    }

    #[Computed]
    public function companies()
    {
        return Cache::remember('companies-v1', 86400, fn() => Company::all(['id', 'company_name']));
    }

    public function render()
    {
        return view('livewire.announcement.form-announcement', [
            'profesions' => $this->profesions,
            'locations' => $this->locations,
            'areas' => $this->areas,
            'companies' => $this->companies,
        ]);
    }
}
