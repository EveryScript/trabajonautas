<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use Livewire\Component;

class RecentAnnouncement extends Component
{
    public $announcements;

    public function mount()
    {
        $this->announcements = Announcement::where('expiration_time', '>=', now())
            ->orderBy('updated_at', 'DESC')->limit(8)->get();
    }
    public function render()
    {
        return <<<'HTML'
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            @forelse ($announcements as $announcement)
            <a href="{{ $announcement->pro && (!auth()->check() || !auth()->user()->hasRole(env('PRO_CLIENT_ROLE')))
                ? route('purchase')
                : route('result', ['id' => $announcement->id]) }}"
                wire:navigate>
                <x-card-announce logo_url="{{ $announcement->company ? $announcement->company->company_image : '' }}"
                    pro="{{ $announcement->pro }}">
                    <x-slot name="area">{{ $announcement->area ? $announcement->area->area_name : '' }}</x-slot>
                    <x-slot name="title">{{ $announcement->announce_title }}</x-slot>
                    <x-slot name="company">{{ $announcement->company ? $announcement->company->company_name : '' }}</x-slot>
                    <x-slot name="locations">
                        {{ $announcement->locations[0]->location_name }}
                        @if ($announcement->locations->count() > 1)
                            <i class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                        @endif
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
