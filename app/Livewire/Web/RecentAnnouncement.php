<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use Livewire\Component;

class RecentAnnouncement extends Component
{
    public $announcements;
    public function mount()
    {
        $this->announcements = Announcement::take(8)->orderBy('updated_at', 'DESC')->get();
    }
    public function render()
    {
        return <<<'HTML'
        <div class="grid grid-cols-2 gap-4 mb-12">
            @forelse ($announcements as $announcement)
            <a href="{{ route('result', ['id' => $announcement->id]) }}" wire:navigate>
                <x-card-announce logo_url="{{ $announcement->company ? $announcement->company->company_image : '' }}">
                    <x-slot name="area">{{ $announcement->area ? $announcement->area->area_name : '' }}</x-slot>
                    <x-slot name="title">{{ $announcement->announce_title }}</x-slot>
                    <x-slot name="company">{{ $announcement->company ? $announcement->company->company_name : '' }}</x-slot>
                    <x-slot name="locations">
                        @forelse ($announcement->locations as $location)
                            <span>{{ $location->location_name }}</span>
                        @empty
                            <span>Sin ubicación</span>
                        @endforelse
                    </x-slot>
                    @if($announcement->expiration_time < Carbon\Carbon::now())
                        <x-slot name="expiration">
                            Expiró {{ (new Carbon\Carbon($announcement->expiration_time))->diffForHumans() }}
                        </x-slot>
                    @endif
                </x-card-announce>
            </a>
            @empty
                <span class="text-tbn-dark text-sm">No hay elementos para mostrar</span>
            @endforelse
        </div>
        HTML;
    }
}
