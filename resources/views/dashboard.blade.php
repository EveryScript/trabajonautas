@if (auth()->user()->hasRole('ADMIN|USER'))
    <x-app-layout>
        @livewire('panel.dashboard-user')
    </x-app-layout>
@else
    @if (!auth()->user()->register_completed)
        <x-guest-layout>
            @livewire('panel.first-steps', ['user_id' => auth()->user()->id])
        </x-guest-layout>
    @elseif (auth()->user()->hasPendingPayment())
        <x-guest-layout>
            @livewire('panel.pending-payment', ['user_id' => auth()->user()->id])
        </x-guest-layout>
    @else
        <x-app-layout>
            @livewire('panel.dashboard-client', ['user_id' => auth()->user()->id])
        </x-app-layout>
    @endif
@endif
