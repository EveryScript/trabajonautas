<?php

namespace App\Livewire\Announcement;

use App\Jobs\SendAnnouncementNotifications;
use App\Livewire\Forms\AnnouncementForm;
use App\Models\Announcement;
use App\Models\Area;
use App\Models\Company;
use App\Models\Location;
use App\Models\Profesion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormAnnouncement extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?int $id = null; // Edit

    public AnnouncementForm $announcement;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['USER', 'ADMIN']), 403);
    }

    public function mount($id = null)
    {
        if ($id !== null) {
            $this->id = (int) $id;
            $this->announcement->edit($this->id);
        }
    }

    public function save()
    {
        $this->announcement->user_id = auth()->user()->id;
        $announce_saved = $this->announcement->save();
        if ($this->announcement->pro && ! $this->announcement->notification_sent) {
            if ($this->announcement->scheduled_at) {
                // Scheduled notification
                $delay = Carbon::parse($this->announcement->scheduled_at);
                SendAnnouncementNotifications::dispatch($announce_saved)->delay($delay);
            } else {
                // Inmediately
                SendAnnouncementNotifications::dispatch($announce_saved);
            }
        }
        $this->redirectRoute('announcement', navigate: true);
    }

    public function update()
    {
        $this->announcement->update($this->id);
        $announce_updated = Announcement::find($this->id);
        if ($this->announcement->pro && ! $this->announcement->notification_sent) {
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

    public function deleteCurrentFile(int $fileId): void
    {
        abort_unless($this->id !== null, 404);

        $announcement = Announcement::query()->findOrFail($this->id);
        $announceFile = $announcement->announceFiles()->findOrFail($fileId);

        if (Storage::disk('public')->exists($announceFile->url)) {
            Storage::disk('public')->delete($announceFile->url);
        }

        $announceFile->delete();
        $this->announcement->current_files = collect($this->announcement->current_files)
            ->where('id', '!=', $fileId)
            ->values();
    }

    public function professionsForArea(int $areaId): array
    {
        abort_unless(Area::query()->whereKey($areaId)->exists(), 404);

        return Profesion::query()
            ->whereHas('areas', fn ($query) => $query->where('areas.id', $areaId))
            ->orderBy('profesion_name')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    #[Computed]
    public function profesions()
    {
        return Cache::remember('announcement_profesions_with_areas', 86400, function () {
            return Profesion::with('areas')->get()->map(function ($p) {
                return [
                    'id' => (int) $p->id,
                    'profesion_name' => $p->profesion_name,
                    'area_ids' => $p->areas->pluck('id')->map(fn ($id) => (int) $id)->toArray(),
                ];
            })->toArray();
        });
    }

    #[Computed]
    public function locations()
    {
        return Cache::remember('locations', 86400, fn () => Location::all(['id', 'location_name']));
    }

    #[Computed]
    public function areas()
    {
        return Cache::remember('areas', 86400, fn () => Area::all(['id', 'area_name']));
    }

    #[Computed]
    public function companies()
    {
        return Cache::remember('companies', 86400, fn () => Company::all(['id', 'company_name']));
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
