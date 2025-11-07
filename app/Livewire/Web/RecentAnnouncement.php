<?php

namespace App\Livewire\Web;

use App\Models\Announcement;
use App\Traits\CheckClientsProVerified;
use Livewire\Component;

class RecentAnnouncement extends Component
{
    use CheckClientsProVerified;

    public $announcements;
    public $client_pro_verified = false;

    public function mount()
    {
        $this->client_pro_verified = $this->isClientProVerified();
        $this->announcements = Announcement::where('expiration_time', '>=', now())
            ->orderBy('updated_at', 'DESC')->limit(6)->get();
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
        return <<<'HTML'
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-12">
            @forelse ($announcements as $announce)
            <a href="{{ $this->isAnnouncePro($announce->pro) ? route('result', ['id' => $announce->id]) : route('purchase-cards') }}"
                    wire:navigate wire:key='announce-{{ $announce->id }}'>
                <x-card-announce logo_url="{{ $announce->company ? $announce->company->company_image : 'empresas/tbn-default.webp' }}"
                    pro="{{ $announce->pro }}">
                    <x-slot name="area">
                        {{ $announce->area ? $announce->area->area_name : '' }}
                    </x-slot>
                    <x-slot name="title">{{ $announce->announce_title }}</x-slot>
                    <x-slot name="company">{{ $announce->company ? $announce->company->company_name : '' }}</x-slot>
                    <x-slot name="locations">
                        {{ $announce->locations[0]->location_name }}
                        @if ($announce->locations->count() > 1)
                            <i class="fas fa-ellipsis-h inline-block px-1 text-xs bg-gray-200 rounded-lg"></i>
                        @endif
                    </x-slot>
                    @if($announce->expiration_time < Carbon\Carbon::now())
                        <x-slot name="expiration">
                            Expiró {{ (new Carbon\Carbon($announce->expiration_time))->diffForHumans() }}
                        </x-slot>
                    @endif
                </x-card-announce>
            </a>
            @empty
            <div class="col-span-2">
                <picture class="block mb-2">
                    <img src="{{ asset('storage/img/empty.webp') }}" alt="empty" class="max-w-[8rem] mx-auto mb-2">
                </picture>
                <p class="text-tbn-dark text-sm">Nuevas convocatorias en camino</p>
            </div>
            @endforelse
        </div>
        HTML;
    }
}
