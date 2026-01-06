<section class="mt-4">
    <div x-data="content" class="bg-white dark:bg-tbn-dark rounded-md shadow-md px-7 py-5">
        <x-title-app>
            <x-slot name="title_page">Bienvenido {{ Auth::user()->name }}</x-slot>
            <x-slot name="description_page">Esta es la actividad más reciente en Trabajonautas.com</x-slot>
            <x-slot name="search_field">
                <div class="h-full sm:h-10 flex flex-col sm:flex-row gap-1">
                    <x-input class="dark:bg-tbn-dark dark:text-white" type="date" x-model="startDate" />
                    <x-input class="dark:bg-tbn-dark dark:text-white" type="date" x-model="endDate" />
                    <x-button type="button" x-on:click="processData" x-bind:disabled="!startDate || !endDate">
                        <span wire:loading.remove>Procesar</span>
                        <span wire:loading><i class="fas fa-spinner text-sm animate-spin"></i></span>
                    </x-button>
                </div>
            </x-slot>
        </x-title-app>
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Tab menu -->
            <ul class="min-w-lg grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 dark:text-tbn-light">
                <li x-on:click="tab_option = 1" class="mb-px transition-colors duration-200 text-center">
                    <a href="#" :class="tab_option === 1 ? tab_active_class : tab_inactive_class"
                        class="bg-white dark:bg-tbn-dark block text-sm py-3 px-6 font-medium">
                        Clientes según cuenta
                    </a>
                </li>
                <li x-on:click="tab_option = 2" class="mb-px transition-colors duration-200 text-center">
                    <a href="#" :class="tab_option === 2 ? tab_active_class : tab_inactive_class"
                        class="bg-white dark:bg-tbn-dark block text-sm py-3 px-6 font-medium">
                        Clientes según edad
                    </a>
                </li>
                <li x-on:click="tab_option = 3" class="mb-px transition-colors duration-200 text-center">
                    <a href="#" :class="tab_option === 3 ? tab_active_class : tab_inactive_class"
                        class="bg-white dark:bg-tbn-dark block text-sm py-3 px-6 font-medium">
                        Clientes según género
                    </a>
                </li>
                <li x-on:click="tab_option = 4" class="mb-px transition-colors duration-200 text-center">
                    <a href="#" :class="tab_option === 4 ? tab_active_class : tab_inactive_class"
                        class="bg-white dark:bg-tbn-dark block text-sm py-3 px-6 font-medium">
                        Clientes según grado académico
                    </a>
                </li>
                <li x-on:click="tab_option = 5" class="mb-px transition-colors duration-200 text-center">
                    <a href="#" :class="tab_option === 5 ? tab_active_class : tab_inactive_class"
                        class="bg-white dark:bg-tbn-dark block text-sm py-3 px-6 font-medium">
                        Clientes por ubicación
                    </a>
                </li>
                <li x-on:click="tab_option = 6" class="mb-px transition-colors duration-200 text-center">
                    <a href="#" :class="tab_option === 6 ? tab_active_class : tab_inactive_class"
                        class="bg-white dark:bg-tbn-dark block text-sm py-3 px-6 font-medium">
                        Clientes por profesión
                    </a>
                </li>
            </ul>
            <!-- Tab content -->
            <div class="flex-1 dark:text-tbn-light">
                <!-- Clients by account type chart -->
                <div x-show="tab_option === 1" x-transition:enter.duration.300ms>
                    <livewire:Charts.ClientsAccount :startDate="$start_date" :endDate="$end_date" :labels="$labels"
                        wire:key='clients-account-chart'>
                </div>
                <!-- Clients by age chart -->
                <div x-show="tab_option === 2" x-transition:enter.duration.300ms>
                    <livewire:Charts.ClientsAge :startDate="$start_date" :endDate="$end_date" :labels="$labels"
                        wire:key='clients-age-chart'>
                </div>
                <!-- Clients by gender chart -->
                <div x-show="tab_option === 3" x-transition:enter.duration.300ms>
                    <livewire:Charts.ClientsGender :startDate="$start_date" :endDate="$end_date" :labels="$labels"
                        wire:key='clients-gender-chart'>
                </div>
                <!-- Clients by grade profile chart -->
                <div x-show="tab_option === 4" x-transition:enter.duration.300ms>
                    <livewire:Charts.ClientsGrade :startDate="$start_date" :endDate="$end_date" :labels="$labels"
                        wire:key='clients-grade-chart'>
                </div>
                <!-- Clients by location chart -->
                <div x-show="tab_option === 5" x-transition:enter.duration.300ms>
                    <livewire:Charts.ClientsLocation :startDate="$start_date" :endDate="$end_date" :labels="$labels"
                        wire:key='clients-location-chart'>
                </div>
                <!-- Clients by profesion chart -->
                <div x-show="tab_option === 6" x-transition:enter.duration.300ms>
                    <livewire:Charts.ClientsProfesion :startDate="$start_date" :endDate="$end_date" :labels="$labels"
                        wire:key='clients-profesion-chart'>
                </div>
            </div>
        </div>
    </div>
</section>
@script
    <script>
        Alpine.data('content', () => ({
            // Base
            tab_option: 1,
            tab_active_class: 'border border-tbn-primary rounded text-tbn-primary',
            tab_inactive_class: 'hover:text-tbn-secondary',
            show_dropdown: false,
            startDate: '',
            endDate: '',
            processData() {
                if (this.startDate && this.endDate)
                    $wire.setRangeDate(this.startDate, this.endDate)
            }
        }))
    </script>
@endscript
