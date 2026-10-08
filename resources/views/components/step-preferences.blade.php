<div x-show="step === 5" x-cloak x-transition:enter.duration.300ms>
    <h5 class="mb-2 font-bold text-md dark:text-white">Preferencias de cliente (opcional)</h5>
    <p class="mb-4 text-sm text-tbn-secondary dark:text-tbn-light">
        Excelente... ya casi tenemos todo listo para enviarte exactamente las convocatorias que necesitas y que encajen
        solo con tu profesión, en tu departamento y a nivel nacional... Adicionalmente también te mandamos convocatorias
        de "Pasantías", "Voluntariados" y otras en las que solo es necesario ser bachiller para postular. Pero si no te
        interesan ese tipo de convocatorias puedes DESACTIVARLAS ahora.
    </p>
    <template x-for="type in announcementTypes" :key="'type-' + type.id">
        <li
            class="flex items-center justify-between gap-4 px-5 py-2 mb-4 transition-all duration-200 bg-white border border-gray-200 shadow-sm cursor-pointer dark:bg-tbn-dark dark:border-tbn-secondary/60 hover:border-tbn-primary/70 dark:hover:border-tbn-primary/70 rounded-xl hover:shadow-md">
            <div class="w-full mb-2">
                <p class="font-medium text-black text-md dark:text-tbn-primary">
                    Desactivar el envío de convocatorias para "<span x-text="type.name"></span>"
                </p>
                <p class="text-xs text-tbn-dark dark:text-white">
                    <span x-text="type.description"></span>
                </p>
            </div>
            <input type="checkbox" x-model="selected_announcement_types" :value="type.id" :id="'type-' + type.id"
                name="type[]" class="hidden peer">

            <label :for="'type-' + type.id"
                class="relative min-w-12 w-12 h-7 bg-gray-200 dark:bg-tbn-secondary peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-orange-300 rounded-full cursor-pointer peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tbn-primary shrink-0 transition-all">
            </label>
        </li>
    </template>
    <p class="my-4 text-sm text-tbn-secondary dark:text-tbn-light">Si deseas puedes cambiar estas opciones una vez
        finalizado el registro.</p>
    <div class="flex justify-between mt-4">
        <x-secondary-button type="button" x-on:click="step = 4">
            Anterior</x-secondary-button>
        <x-button type="button" x-on:click="step = 6">
            Siguiente</x-button>
    </div>
</div>
