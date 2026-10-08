<?php

namespace App\Livewire\Panel;

use App\Models\AnnouncementType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DashboardPreferences extends Component
{
    public string $title = '';
    public string $description = '';
    public array $selected = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $excludedIds = $user
            ->excludedAnnouncementTypes()
            ->pluck('announcement_types.id')
            ->toArray();

        $allTypeIds = $this->types->pluck('id')->toArray();
        $this->selected = array_values(array_diff($allTypeIds, $excludedIds));
    }

    #[Computed(cache: true, key: 'announcement_types_all')]
    public function types()
    {
        return AnnouncementType::select('id', 'name', 'description')->get();
    }

    public function save(): void
    {

        /** @var User $user */
        $user = Auth::user();
        $allTypeIds = $this->types->pluck('id')->toArray();
        $selectedInts = array_map('intval', $this->selected);
        $excludedIds = array_values(array_diff($allTypeIds, $selectedInts));
        $user->excludedAnnouncementTypes()->sync($excludedIds);
        $this->dispatch('preferences-updated');
    }

    public function render()
    {
        return view('livewire.panel.dashboard-preferences', [
            'types' => $this->types,
        ]);
    }
}
