<div x-show="step === 5" x-cloak x-transition:enter.duration.300ms>
    <h5 class="mb-2 font-bold text-md dark:text-white">Elige una cuenta</h5>
    <ul class="grid grid-cols-1 gap-1 md:grid-cols-3">
        <template x-for="accountType in accountTypes">
            <li :key="'account-' + accountType.id"
                class="transition-colors duration-300 hover:bg-gray-50 dark:hover:bg-slate-900">
                <input type="radio" x-on:click="setAccountData(accountType)" :id="'account-' + accountType.id"
                    :value="accountType.id" class="hidden peer" name="account_type">
                <label :for="'account-' + accountType.id"
                    class="block p-6 bg-white border-2 border-gray-200 rounded-lg shadow-lg cursor-pointer dark:bg-tbn-dark dark:text-white dark:border-tbn-secondary peer-checked:border-tbn-primary">
                    <h2 x-text="accountType.name"
                        class="text-xl font-semibold capitalize text-tbn-secondary hover:bg-tbn-dark dark:text-tbn-primary">
                    </h2>
                    <div class="mt-2 md:mt-2">
                        <span x-text="accountType.price+' Bs.'"
                            class="text-3xl font-bold md:text-4xl text-tbn-secondary dark:text-tbn-light"></span>
                        <span
                            x-text="accountType.duration_days == 0 ? '/ Siempre' : '/ '+accountType.duration_days+' dias'"
                            class="font-medium text-tbn-dark dark:text-tbn-light"></span>
                    </div>
                    <ul class="mt-3 space-y-2 text-xs md:mt-6 md:text-sm">
                        <li class="flex items-center">
                            <i class="mr-2 text-green-500 fas fa-check"></i>
                            Convocatorias estándar
                        </li>
                        <li class="flex items-center">
                            <i x-show="accountType.id == 1" class="mr-2 text-red-500 fas fa-times"></i>
                            <i x-show="accountType.id == 2 || accountType.id == 3"
                                class="mr-2 text-green-500 fas fa-check"></i>
                            Convocatorias PRO
                        </li>
                        <li class="flex items-center">
                            <i x-show="accountType.id == 1 || accountType.id == 2"
                                class="mr-2 text-red-500 fas fa-times"></i>
                            <i x-show="accountType.id == 3" class="mr-2 text-green-500 fas fa-check"></i>
                            Notificaciones en tiempo real
                        </li>
                    </ul>
                </label>
            </li>
        </template>
    </ul>
    <div class="flex justify-between mt-4">
        <x-secondary-button type="button" x-on:click="step = 4">
            Anterior</x-secondary-button>
        <x-button type="submit" x-on:click="isProAccountSelected" x-bind:disabled="!account_type_id">
            <span wire:loading.remove>Siguiente</span>
            <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
        </x-button>
    </div>
</div>
