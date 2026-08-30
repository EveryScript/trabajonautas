<section class="mx-auto mt-4">
    <div x-data="content" class="space-y-6">
        <div
            class="flex flex-col justify-between gap-4 p-6 bg-white border shadow-sm dark:bg-tbn-dark rounded-2xl border-slate-200/80 dark:border-neutral-800 md:flex-row md:items-center">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Exportar convocatorias</h1>
                <p class="mt-1 text-xs text-tbn-light sm:text-sm">Sigue el proceso para extraer las convocatorias y
                    archivos en un rango definido.</p>
            </div>

            <div
                class="flex items-center gap-2 bg-slate-100 dark:bg-tbn-primary/10 px-3.5 py-1.5 rounded-full text-xs font-semibold text-slate-600 dark:text-slate-300 self-start md:self-auto">
                <span class="w-2 h-2 rounded-full bg-tbn-primary animate-pulse"></span>
                Flujo Guiado
            </div>
        </div>

        <!-- Select Range -->
        <div
            class="p-6 bg-white border shadow-sm dark:bg-tbn-dark rounded-2xl border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-3">
                    <span
                        class="flex items-center justify-center w-8 h-8 text-sm font-bold border rounded-xl bg-tbn-primary/10 text-tbn-primary border-tbn-primary/20">
                        1
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Selección de rango de Fechas</h2>
                        <p class="text-xs text-tbn-light">Define el período para filtrar las convocatorias</p>
                    </div>
                </div>

                <!-- Tag -->
                <template x-if="from && to">
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800">
                        <i class="fa-solid fa-check"></i>
                        Rango válido
                    </span>
                </template>
            </div>

            <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-2" wire:ignore>
                    <x-input id="dateRange" type="text" readonly
                        placeholder="Haz clic para seleccionar el rango..."></x-input>
                </div>

                <!-- Range defined -->
                <div
                    class="p-3 space-y-1 text-xs border bg-slate-50 dark:bg-tbn-primary/10 rounded-xl border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between text-tbn-light">
                        <span>Desde:</span>
                        <span class="font-semibold text-tbn-dark dark:text-white" x-text="from || '---'"></span>
                    </div>
                    <div class="flex justify-between text-tbn-light">
                        <span>Hasta:</span>
                        <span class="font-semibold text-tbn-dark dark:text-white" x-text="to || '---'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Polling de fondo cuando hay tareas en procesamiento -->
        @if ($statusExcel === 'procesing' || $statusZip === 'procesing' || $statusDeleting === 'procesing')
            <div wire:poll.3s="checkStatus"></div>
        @endif

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

            <!-- Step 1: Excel -->
            <div class="relative flex flex-col justify-between p-6 transition-all duration-300 bg-white border dark:bg-tbn-dark rounded-2xl"
                :class="{
                    'border-green-300 ring-1 ring-emerald-300 shadow-sm': '{{ $statusExcel }}'
                    === 'ready',
                    'border-tbn-primary/60 ring-2 ring-tbn-primary/20 shadow-md': from && to && '{{ $statusExcel }}'
                    !== 'ready',
                    'border-slate-200/80 dark:border-neutral-800 opacity-60 bg-slate-50/50': !from || !to
                }">

                <div class="flex items-center justify-between mb-4">
                    <span
                        class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg bg-tbn-primary/10 text-tbn-primary">
                        Paso 1
                    </span>
                    @if ($statusExcel === 'ready')
                        <span
                            class="flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md">
                            <i class="fa-solid fa-check"></i>
                            Listo
                        </span>
                    @endif
                </div>

                <div class="mb-6">
                    <div
                        class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-colors {{ $statusExcel === 'ready' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30' : 'bg-tbn-primary/10 text-tbn-primary' }}">
                        <i class="fa-solid fa-file-excel text-lg"></i>
                    </div>
                    <h3 class="mb-1 text-lg font-bold text-slate-900 dark:text-white">Exportar Excel</h3>
                    <p class="text-xs leading-relaxed text-tbn-light">Genera la hoja de cálculo con la información
                        estructurada de las convocatorias.</p>
                </div>

                <div>
                    @if (is_null($statusExcel))
                        <button type="button" wire:click="startExcel" :disabled="!from || !to"
                            class="w-full group inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 shadow-sm"
                            :class="(from && to) ?
                            'bg-tbn-primary hover:opacity-90 text-white shadow-tbn-primary/20 active:scale-[0.98]' :
                            'bg-slate-100 text-slate-400 border border-slate-200 dark:bg-tbn-primary/10 dark:border-slate-700 dark:text-slate-600 cursor-not-allowed'">
                            <i class="fa-solid fa-file-excel"></i>
                            <span>Generar Excel</span>
                        </button>
                    @elseif ($statusExcel === 'procesing')
                        <div
                            class="w-full flex items-center justify-center gap-2 px-5 py-3.5 bg-tbn-primary/10 border border-tbn-primary/30 rounded-xl text-tbn-primary font-semibold text-sm">
                            <i class="fa-solid fa-spinner animate-spin"></i>
                            <span>Procesando...</span>
                        </div>
                    @else
                        <a href="{{ $urlExcel }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition-all shadow-md shadow-emerald-600/20">
                            <i class="fa-solid fa-download"></i>
                            <span>Descargar Excel</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Step 2: Zip -->
            <div class="relative flex flex-col justify-between p-6 transition-all duration-300 bg-white border dark:bg-tbn-dark rounded-2xl"
                :class="{
                    'border-emerald-300 ring-1 ring-emerald-300 shadow-sm': '{{ $statusZip }}'
                    === 'ready',
                    'border-tbn-primary/60 ring-2 ring-tbn-primary/20 shadow-md': '{{ $statusExcel }}'
                    === 'ready' && '{{ $statusZip }}'
                    !== 'ready',
                    'border-slate-200/80 dark:border-neutral-800 opacity-60 bg-slate-50/50': '{{ $statusExcel }}'
                    !== 'ready'
                }">

                <div class="flex items-center justify-between mb-4">
                    <span
                        class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg {{ $statusExcel === 'ready' ? 'bg-tbn-primary/10 text-tbn-primary' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                        Paso 2
                    </span>
                    @if ($statusZip === 'ready')
                        <span
                            class="flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md">
                            <i class="fa-solid fa-check"></i>
                            Listo
                        </span>
                    @endif
                </div>

                <div class="mb-6">
                    <div
                        class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-colors {{ $statusZip === 'ready' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30' : ($statusExcel === 'ready' ? 'bg-tbn-primary/10 text-tbn-primary' : 'bg-slate-100 text-slate-400 dark:bg-slate-800') }}">
                        <i class="fa-solid fa-file-zipper"></i>
                    </div>
                    <h3 class="mb-1 text-lg font-bold text-slate-900 dark:text-white">Exportar archivos</h3>
                    <p class="text-xs leading-relaxed text-tbn-light">Comprime y descarga todos los archivos vinculados
                        a las convocatorias. Puede tardar <span class="font-bold">varios minutos</span></p>
                </div>

                <div>
                    @if (is_null($statusZip))
                        <button type="button" wire:click="startZip" {{ $statusExcel !== 'ready' ? 'disabled' : '' }}
                            class="w-full group inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 shadow-sm {{ $statusExcel === 'ready' ? 'bg-tbn-primary hover:opacity-90 text-white shadow-tbn-primary/20 active:scale-[0.98]' : 'bg-slate-100 text-slate-400 border border-slate-200 dark:bg-tbn-primary/10 dark:border-slate-700 dark:text-slate-600 cursor-not-allowed' }}">
                            <i class="fa-solid fa-file-zipper"></i>
                            <span>Generar ZIP</span>
                        </button>
                    @elseif ($statusZip === 'procesing')
                        <div
                            class="w-full flex items-center justify-center gap-2 px-5 py-3.5 bg-tbn-primary/10 border border-tbn-primary/30 rounded-xl text-tbn-primary font-semibold text-sm">
                            <i class="fa-solid fa-spinner animate-spin"></i>
                            <span>Comprimiendo...</span>
                        </div>
                    @else
                        @if ($zipWithoutFiles)
                            <div
                                class="p-3 text-xs font-medium text-center border bg-amber-50 border-amber-200 text-amber-800 rounded-xl">
                                No hay archivos en este rango.
                            </div>
                        @else
                            <a href="{{ $urlZip }}"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition-all shadow-md shadow-emerald-600/20">
                                <i class="fa-solid fa-download"></i>
                                <span>Descargar ZIP</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Step 3: Destroy -->
            <div class="relative flex flex-col justify-between p-6 transition-all duration-300 bg-white border dark:bg-tbn-dark rounded-2xl"
                :class="{
                    'border-red-300 ring-2 ring-red-100 shadow-md': '{{ $statusZip }}'
                    === 'ready' && '{{ $statusDeleting }}'
                    !== 'ready',
                    'border-slate-200/80 dark:border-neutral-800 opacity-60 bg-slate-50/50': '{{ $statusZip }}'
                    !== 'ready'
                }">

                <div class="flex items-center justify-between mb-4">
                    <span
                        class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg {{ $statusZip === 'ready' ? 'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                        Paso 3
                    </span>
                    @if ($statusDeleting === 'ready')
                        <span
                            class="flex items-center gap-1 text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                            Eliminado
                        </span>
                    @endif
                </div>

                <div class="mb-6">
                    <div
                        class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-colors {{ $statusZip === 'ready' ? 'bg-red-100 text-red-600 dark:bg-red-900/30' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">
                        <i class="fa-solid fa-trash-can"></i>
                    </div>
                    <h3 class="mb-1 text-lg font-bold text-slate-900 dark:text-white">Eliminar registros</h3>
                    <p class="text-xs leading-relaxed text-tbn-light">Elimina las convocatorias del
                        rango. Esta acción es <strong class="text-slate-700 dark:text-slate-300">Irreversible.</strong>
                    </p>
                </div>

                <div>
                    @if (is_null($statusDeleting))
                        @if ($statusZip === 'ready')
                            <div class="space-y-3">
                                <div
                                    class="p-3 space-y-1 text-xs border bg-slate-50 dark:bg-tbn-primary/10 rounded-xl border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center justify-between">
                                        <span class="text-tbn-light">Convocatorias expiradas a eliminar:</span>
                                        <span class="font-bold text-red-600">{{ $totalDeletable }}</span>
                                    </div>
                                    @if ($totalNotDeletable > 0)
                                        <div
                                            class="flex items-center gap-1.5 pt-1 mt-1 text-amber-700 dark:text-amber-400 border-t border-slate-200 dark:border-slate-700">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            <span>{{ $totalNotDeletable }} seguirán vigentes y no se eliminarán</span>
                                        </div>
                                    @endif
                                </div>

                                <x-input type="text" wire:model.live="textConfirmation"
                                    placeholder="Escribe 'ELIMINAR' para confirmar"></x-input>
                                <button type="button" wire:click="startDeleting"
                                    {{ $textConfirmation !== 'ELIMINAR' || $totalDeletable === 0 ? 'disabled' : '' }}
                                    class="w-full group inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 shadow-sm {{ $textConfirmation === 'ELIMINAR' && $totalDeletable > 0 ? 'bg-red-600 hover:bg-red-700 text-white shadow-red-500/20 active:scale-[0.98]' : 'bg-slate-100 text-slate-400 border border-slate-200 dark:bg-tbn-primary/10 dark:border-slate-700 cursor-not-allowed' }}">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span>Eliminar {{ $totalDeletable }} registros</span>
                                </button>
                            </div>
                        @else
                            <button type="button" disabled
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-slate-100 text-slate-400 border border-slate-200 dark:bg-tbn-primary/10 dark:border-slate-700 dark:text-slate-600 rounded-xl font-semibold text-sm cursor-not-allowed">
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Completa el paso 2</span>
                            </button>
                        @endif
                    @elseif ($statusDeleting === 'procesing')
                        <div
                            class="w-full flex items-center justify-center gap-2 px-5 py-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 font-semibold text-sm">
                            <i class="fa-solid fa-spinner animate-spin"></i>
                            <span>Eliminando...</span>
                        </div>
                    @else
                        <div class="space-y-3">
                            <div
                                class="p-3 text-xs font-medium text-center border bg-emerald-50 border-emerald-200 text-emerald-800 rounded-xl">
                                <i class="fa-solid fa-check inline mr-2"></i> Registros eliminados correctamente
                            </div>
                            <button type="button" wire:click="restartAll"
                                class="w-full text-xs font-semibold text-center underline transition-colors text-tbn-light hover:text-slate-800">
                                Iniciar un nuevo proceso
                            </button>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>

@script
    <script>
        Alpine.data('content', () => ({
            from: '',
            to: '',
            init() {
                flatpickr("#dateRange", {
                    mode: "range",
                    dateFormat: "d/m/Y",
                    "locale": "es",
                    onChange: (selectedDates, dateStr, instance) => {
                        if (selectedDates.length === 2) {
                            this.from = instance.formatDate(selectedDates[0], 'Y-m-d');
                            this.to = instance.formatDate(selectedDates[1], 'Y-m-d');
                            $wire.set('from', this.from);
                            $wire.set('to', this.to);
                        } else {
                            this.from = '';
                            this.to = '';
                            $wire.set('from', '');
                            $wire.set('to', '');
                        }
                    }
                });
            }
        }))
    </script>
@endscript
