<div>
    <div class="px-4 mb-4 text-center">
        <i class="fas fa-crown text-[3rem] text-tbn-primary mb-4"></i>
        <h5 class="text-xl font-medium text-tbn-dark dark:text-white">
            Adquiere tu cuenta de <span class="text-tbn-primary">Trabajonautas PRO</span> ahora mismo</h5>
        <p class="text-sm text-tbn-dark dark:text-tbn-light">
            Convocatorías exclusivas y nuevas opciones de búsqueda cada día al alcance de ti. </p>
    </div>
    <div class="grid gap-8 p-4 mt-4 mb-12 lg:grid-cols-3 md:p-8">
        @foreach ($account_types as $account_type)
            <div wire:key='account-{{ $account_type->id }}'
                class="relative {{ $account_type->id == 1 ? 'order-1' : ($account_type->id == 2 ? 'order-3' : 'order-2') }}">
                @if ($account_type->id == 3)
                    <div class="absolute left-0 right-0 flex justify-center -top-4">
                        <span
                            class="flex items-center gap-1 px-4 py-1 text-sm font-medium text-white rounded-full bg-gradient-to-r from-tbn-primary to-tbn-secondary">
                            <i class="fas fa-star"></i> Mejor opción
                        </span>
                    </div>
                @endif
                <div
                    class="flex flex-col justify-between h-full bg-white dark:bg-tbn-dark border border-gray-200 dark:border-tbn-secondary rounded-lg shadow-sm
                        {{ $account_type->id == 3 ? 'border-2 border-tbn-secondary dark:border-tbn-primary' : '' }}">
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold capitalize text-tbn-dark dark:text-tbn-primary">
                            {{ $account_type->name }}</h3>
                        <p class="mt-2 text-sm text-tbn-secondary dark:text-white">
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
                            <span class="text-4xl font-bold text-tbn-dark dark:text-white">{{ $account_type->price }}
                                Bs.</span>
                            <span class="ml-2 text-tbn-secondary dark:text-white">
                                {{ $account_type->duration_days == 0 ? 'Siempre' : '/ ' . $account_type->duration_days . ' dias' }}
                            </span>
                        </div>
                        <ul class="my-6 space-y-2 text-sm text-tbn-dark dark:text-white">
                            <li class="flex items-center">
                                <i class="mr-2 text-green-500 fas fa-check"></i> Convocatorias estandar
                            </li>
                            <li class="flex items-center">
                                @if ($account_type->id == 1)
                                    <i class="mr-2 fas fa-times text-tbn-primary"></i>
                                @else
                                    <i class="mr-2 text-green-500 fas fa-check"></i>
                                @endif
                                Convocatorias Premium
                            </li>
                            <li class="flex items-center">
                                @if ($account_type->id == 1 || $account_type->id == 2)
                                    <i class="mr-2 fas fa-times text-tbn-primary"></i>
                                @else
                                    <i class="mr-2 text-green-500 fas fa-check"></i>
                                @endif
                                Notificaciones en tiempo real
                            </li>
                        </ul>
                    </div>
                    <div
                        class="p-6 border-t border-gray-200 rounded-b-lg dark:border-tbn-secondary bg-gray-50 dark:bg-tbn-dark">
                        @php
                            $url = route('register');
                            if ($client && $client->account) {
                                $typeId = intval($account_type->id);
                                if ($typeId > $client->account->account_type_id) {
                                    $url = route('purchase-account', ['account_type_id' => $typeId]);
                                }
                            }
                            $buttonComponent = intval($account_type->id) === 3 ? 'button' : 'secondary-button';
                        @endphp
                        <a href="{{ $url }}" wire:navigate>
                            <x-dynamic-component :component="$buttonComponent" class="w-full">
                                Obtener <i class="ml-2 fas fa-arrow-right"></i>
                            </x-dynamic-component>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
