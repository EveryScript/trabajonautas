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
    public array $excluded = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->excluded = $user
            ->excludedAnnouncementTypes()
            ->pluck('announcement_types.id')
            ->toArray();
    }

    #[Computed(cache: true, key: 'announcement_types_all')]
    public function types()
    {
        return AnnouncementType::orderBy('name')->get();
    }

    public function save(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->excludedAnnouncementTypes()->sync($this->excluded);
        $this->dispatch('preferences-updated');
    }

    public function render()
    {
        return view('livewire.panel.dashboard-preferences', [
            'types' => $this->types,
        ]);
    }
}
