@props(['tbn_coins'])
<div x-show="step === 6" x-cloak x-transition:enter.duration.300ms>
    <h5 class="mb-2 font-bold text-md dark:text-white">Elige una cuenta</h5>
    <ul class="grid grid-cols-1 gap-6 pt-6 md:gap-4 md:grid-cols-3">
        <template x-for="(accountType, index) in accountTypes" :key="'account-' + accountType.id">
            <li class="relative transition-all duration-300"
                :class="accountType.id == 1 ? 'order-1' : (accountType.id == 2 ? 'order-3' : 'order-2')">
                <input type="radio" x-on:click="setAccountData(accountType)" :id="'account-' + accountType.id"
                    :value="accountType.id" class="hidden peer" name="account_type">
                <label :for="'account-' + accountType.id"
                    class="flex flex-col justify-between h-full p-6 transition-all bg-white border cursor-pointer text-tbn-secondary dark:text-white dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary rounded-xl peer-checked:border-tbn-primary peer-checked:ring-2 peer-checked:ring-tbn-primary peer-checked:text-tbn-primary hover:shadow-md hover:border-tbn-primary/50">
                    <div x-show="index === 2" class="absolute left-0 right-0 flex justify-center -top-3.5 z-10">
                        <span
                            class="flex items-center gap-1.5 px-3.5 py-1 text-xs font-semibold text-white rounded-full bg-gradient-to-r from-tbn-primary to-tbn-secondary shadow-sm">
                            <i class="fas fa-star text-[10px]"></i> Mejor opción
                        </span>
                    </div>
                    <div>
                        <h2 x-text="accountType.name"
                            class="text-lg font-bold tracking-wide capitalize text-tbn-secondary dark:text-tbn-primary">
                        </h2>

                        <div class="flex items-baseline gap-1 mt-3">
                            <span x-text="accountType.price+' Bs.'"
                                class="text-3xl font-extrabold md:text-4xl text-tbn-secondary dark:text-tbn-light"></span>
                            <span
                                x-text="accountType.duration_days == 0 ? '/ Siempre' : '/ '+accountType.duration_days+' días'"
                                class="text-xs font-medium text-gray-500 md:text-sm dark:text-tbn-light"></span>
                        </div>
                    </div>
                    <ul
                        class="pt-4 space-y-3 text-xs font-normal border-gray-100 md:text-sm text-tbn-secondary dark:text-white dark:border-slate-800">
                        <li class="flex items-center">
                            <i class="mr-2.5 text-green-500 fas fa-check"></i>
                            Convocatorias Free
                        </li>
                        <li class="flex items-center">
                            <i x-show="accountType.id == 1" class="mr-2.5 text-red-500 fas fa-times"></i>
                            <i x-show="accountType.id != 1" class="mr-2.5 text-green-500 fas fa-check"></i>
                            Convocatorias Premium
                        </li>
                        <li class="flex items-center">
                            <i x-show="accountType.id != 3" class="mr-2.5 text-red-500 fas fa-times"></i>
                            <i x-show="accountType.id == 3" class="mr-2.5 text-green-500 fas fa-check"></i>
                            Notificaciones en tiempo real
                        </li>
                        <li class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i x-show="accountType.id != 3" class="mr-2.5 text-red-500 fas fa-times"></i>
                                <i x-show="accountType.id == 3" class="mr-2.5 text-green-500 fas fa-check"></i>
                                <span>Monedas especiales: {{ $tbn_coins }}</span>
                            </div>
                            <div class="relative inline-block group">
                                <button type="button"
                                    class="p-1 rounded-full text-tbn-primary hover:text-tbn-secondary focus:outline-none">
                                    <i class="text-xs fa-regular fa-circle-question"></i>
                                </button>
                                <div
                                    class="absolute bottom-full right-0 mb-2 hidden group-hover:block w-64 max-w-[85vw] rounded-md bg-neutral-900 px-4 py-3 text-xs text-white shadow-xl transition-opacity z-20">
                                    Estas monedas te permiten ver convocatorias que NO son de tu profesión por si tienes
                                    interés en explorar alguna otra oferta laboral.
                                    <div
                                        class="absolute -mt-1 -translate-x-1/2 border-4 border-transparent top-full right-4 border-t-neutral-900">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </label>
            </li>
        </template>
    </ul>
    <div class="flex justify-between mt-4">
        <x-secondary-button type="button" x-on:click="step = 5">
            Anterior</x-secondary-button>
        <x-button type="submit" x-on:click="isProAccountSelected" x-bind:disabled="!account_type_id">
            <span wire:loading.remove>Siguiente</span>
            <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
        </x-button>
    </div>
</div>
