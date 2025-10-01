<?php

namespace App\Livewire\Web;

use App\Models\AccountType;
use App\Models\User;
use Livewire\Component;

class PurchaseCards extends Component
{
    public $account_types;
    public $client, $pro_verified = false;

    public function mount()
    {
        $this->account_types = AccountType::all();
        if (auth()->check()) {
            $this->client = User::with('account.accountType')->find(auth()->user()->id);
            $this->pro_verified = auth()->user()->roles->pluck('name')->first() === env('CLIENT_ROLE')
                ? $this->client->account->verified_payment : true;
        }
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            <section class="max-w-6xl mx-auto my-10">
                <div class="text-center mb-4 px-4">
                    <i class="fas fa-crown text-[3rem] text-tbn-secondary mb-4"></i>
                    <h5 class="font-medium text-xl">Adquiere tu cuenta de <span class="text-tbn-primary">Trabajonautas
                            PRO</span> ahora mismo</h5>
                    <p class="text-tbn-dark text-sm">Convocatorías exclusivas y nuevas opciones de búsqueda cada día al
                        alcance de ti. </p>
                </div>
                <div class="grid gap-8 mb-12 lg:grid-cols-3 p-4 md:p-8 mt-4">
                    @foreach ($account_types as $account_type)
                        <div class="relative">
                            @if ($account_type->id == 2)
                                <div class="absolute left-0 right-0 flex justify-center -top-4">
                                    <span
                                        class="flex items-center gap-1 px-4 py-1 text-sm font-medium text-white rounded-full bg-gradient-to-r from-tbn-primary to-blue-800">
                                        <i class="fas fa-star"></i> Mejor opción
                                    </span>
                                </div>
                            @endif
                            <div
                                class="flex flex-col justify-between h-full bg-white border border-gray-200 rounded-lg shadow-sm
                                {{ $account_type->id == 2 ? 'border-2 border-tbn-primary' : '' }}">
                                <div class="p-6">
                                    <h3 class="text-2xl font-semibold text-gray-900 capitalize">{{ $account_type->name }}</h3>
                                    <p class="mt-2 text-sm text-gray-600">
                                        @switch($account_type->id)
                                            @case(1)
                                                La mejor opción para comenzar
                                            @break

                                            @case(2)
                                                Convocatorias exclusivas y beneficios al instante
                                            @break

                                            @case(3)
                                                Navega sin límites en nuestra plataforma Trabajonautas.
                                            @break
                                        @endswitch
                                    </p>
                                    <div class="mt-4">
                                        <span class="text-4xl font-bold">{{ $account_type->price }} Bs.</span>
                                        <span class="ml-2 text-gray-600">
                                            {{ $account_type->duration_days == 0 ? 'Siempre' : '/ ' . $account_type->duration_days . ' dias' }}
                                        </span>
                                    </div>
                                    <ul class="my-6 space-y-2 text-sm">
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-500 mr-2"></i> Convocatorias estandar
                                        </li>
                                        <li class="flex items-center">
                                            @if ($account_type->id == 1)
                                                <i class="fas fa-times text-red-500 mr-2"></i>
                                            @else
                                                <i class="fas fa-check text-green-500 mr-2"></i>
                                            @endif
                                            Convocatorias Premium
                                        </li>
                                        <li class="flex items-center">
                                            @if ($account_type->id == 1 || $account_type->id == 2)
                                                <i class="fas fa-times text-red-500 mr-2"></i>
                                            @else
                                                <i class="fas fa-check text-green-500 mr-2"></i>
                                            @endif
                                            Notificaciones en tiempo real
                                        </li>
                                    </ul>
                                </div>
                                <div class="p-6 border-t border-gray-200 rounded-b-lg bg-gray-50">
                                    @if (!$client)
                                        <a href="{{ route('register') }}" wire:navigate
                                            class="block w-full px-4 py-2 font-medium text-center text-white transition-colors rounded-lg bg-gradient-to-r from-tbn-primary to-blue-800 hover:from-blue-800 hover:to-blue-900">
                                            {{ $account_type->id == 1 ? 'Iniciar ahora' : 'Comprar ahora' }}
                                            <i class="fas fa-arrow-right ml-2"></i></a>
                                    @elseif($client->roles->pluck('name')->first() !== env('CLIENT_ROLE'))
                                        <a href="{{ route('dashboard') }}" wire:navigate
                                            class="block w-full px-4 py-2 font-medium text-center text-white transition-colors rounded-lg bg-gradient-to-r from-tbn-primary to-blue-800 hover:from-blue-800 hover:to-blue-900">
                                            {{ $account_type->id == 1 ? 'Iniciar ahora' : 'Comprar ahora' }}
                                            <i class="fas fa-arrow-right ml-2"></i></a>
                                    @elseif($pro_verified || $client->account->account_type_id == 1)
                                        @if ($account_type->id == 1)
                                            <a href="{{ route('dashboard') }}" wire:navigate
                                                class="block w-full px-4 py-2 font-medium text-center text-white transition-colors rounded-lg bg-gradient-to-r from-tbn-primary to-blue-800 hover:from-blue-800 hover:to-blue-900">
                                                {{ $account_type->id == 1 ? 'Iniciar ahora' : 'Comprar ahora' }}
                                                <i class="fas fa-arrow-right ml-2"></i></a>
                                        @else
                                            <a href="{{ route('purchase-account', ['account_type_id' => $account_type->id]) }}"
                                                class="block w-full px-4 py-2 font-medium text-center text-white transition-colors rounded-lg bg-gradient-to-r from-tbn-primary to-blue-800 hover:from-blue-800 hover:to-blue-900">
                                                {{ $account_type->id == 1 ? 'Iniciar ahora' : 'Comprar ahora' }}
                                                <i class="fas fa-arrow-right ml-2"></i></a>
                                        @endif
                                    @else
                                        <a href="{{ route('dashboard') }}" wire:navigate
                                            class="block w-full px-4 py-2 font-medium text-center text-white transition-colors rounded-lg bg-gradient-to-r from-tbn-primary to-blue-800 hover:from-blue-800 hover:to-blue-900">
                                            {{ $account_type->id == 1 ? 'Iniciar ahora' : 'Comprar ahora' }}
                                            <i class="fas fa-arrow-right ml-2"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
        HTML;
    }
}
