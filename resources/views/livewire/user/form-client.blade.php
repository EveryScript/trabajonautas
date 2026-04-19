<section>
    <div class="max-w-2xl divide-y divide-tbn-secondary" x-data="content">
        <!-- Update client -->
        <div class="mb-8">
            <x-title-app>
                <x-slot name="title_page">Editar cliente</x-slot>
                <x-slot name="description_page">Actualiza la información de un cliente</x-slot>
            </x-title-app>
            <form>
                <!-- Client name -->
                <div class="mb-4">
                    <x-label for="name" value="{{ __('Nombre del cliente') }}" />
                    <x-input wire:model='form.name' id="name" type="text" class="block w-full mt-1"
                        placeholder="Andrés Rodriguez" />
                    <x-input-error for="form.name" class="mt-2" />
                </div>
                <!-- Email -->
                <div class="mb-4">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input wire:model='form.email' id="email" type="email" class="block w-full mt-1"
                        placeholder="example@email.com" />
                    <x-input-error for="form.email" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Phone -->
                    <div class="mb-4">
                        <x-label for="phone" value="{{ __('Celular') }}" />
                        <x-input wire:model='form.phone' id="phone" type="text" class="block w-full mt-1"
                            placeholder="65432100" />
                        <x-input-error for="form.phone" class="mt-2" />
                    </div>
                    <!-- Location -->
                    <div class="mb-4">
                        <x-label for="location" value="{{ __('Ubicación') }}" />
                        <x-select wire:model='form.location_id' class="block w-full">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="form.location_id" class="mt-2" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Gender -->
                    @php
                        $genders = [
                            'M' => 'Masculino',
                            'F' => 'Femenino',
                        ];
                    @endphp
                    <div class="mb-4">
                        <x-label for="gender" value="{{ __('Genero') }}" />
                        <x-select wire:model='form.gender' class="block w-full">
                            @foreach ($genders as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="form.gender" class="mt-2" />
                    </div>
                    <!-- Age -->
                    @php
                        $ages = [
                            '1' => 'de 18 a 25 años',
                            '2' => 'de 26 a 32 años',
                            '3' => 'de 33 en adelante',
                        ];
                    @endphp
                    <div class="mb-4">
                        <x-label for="age" value="{{ __('Rango de edad') }}" />
                        <x-select wire:model='form.age' class="block w-full">
                            @foreach ($ages as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="form.age" class="mt-2" />
                    </div>
                </div>
                <!-- Grade profile -->
                <div class="mb-4">
                    <x-label for="grade-profile" value="{{ __('Grado académico') }}" />
                    <x-select wire:model='form.grade_profile_id' class="block w-full" id="grade-profile">
                        @foreach ($grade_profiles as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->profile_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="form.grade_profile_id" class="mt-2" />
                </div>
                <!-- Profesion -->
                <div class="mb-4">
                    <x-label for="profesions">Profesiones</x-label>
                    <div class="mt-1 tbn-tom-select" wire:ignore>
                        <x-select id="profesions" wire:model="form.profesion_id">
                            @foreach ($profesions as $profesion)
                                <option value="{{ $profesion->id }}">{{ $profesion->profesion_name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <x-input-error for="form.profesion_id" class="mt-2" />
                </div>
                <!-- Account type -->
                <div class="mb-4">
                    <x-label for="account" value="{{ __('Tipo de cuenta') }}" />
                    <ul class="grid w-full gap-4 md:grid-cols-3">
                        @foreach ($account_types as $type)
                            <li wire:key='type-{{ $type->id }}'>
                                <input type="radio" id="type-{{ $type->id }}" class="hidden peer"
                                    wire:model='form.account_type_id'
                                    {{ $type->id == $this->form->account_type_id ? 'checked' : '' }}
                                    value="{{ $type->id }}" name="account_type">
                                <label for="type-{{ $type->id }}"
                                    class="inline-flex items-center justify-between w-full p-5 bg-white border rounded-lg cursor-pointer text-tbn-secondary dark:text-white dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                                    <div class="w-2/3">
                                        <div class="w-full text-lg font-semibold">{{ $type->name }}</div>
                                    </div>
                                    <i
                                        class="mr-1 fas {{ $type->id == 1 ? 'fa-leaf text-green-600' : 'fa-crown text-tbn-primary' }}"></i>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    <x-input-error for="form.account_type_id" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-button type="button" x-on:click="confirmUpdateModal" wire:loading.attr='disabled'>
                        <span wire:loading.remove wire:target='save'>Actualizar cliente</span>
                        <span wire:loading wire:target='save'>Actualizando...</span>
                    </x-button>
                    <x-secondary-button type="button" href="{{ route('client') }}"
                        wire:navigate>Cancelar</x-secondary-button>
                </div>
            </form>
        </div>
        <!-- Force delete client -->
        <div class="py-8">
            <x-title-app>
                <x-slot name="title_page">Eliminar cliente</x-slot>
                <x-slot name="description_page">Elimina solamente la información del cliente conservando sus datos
                    relacionados y la información almacenada hasta entonces.</x-slot>
            </x-title-app>
            <div class="mb-4">
                <x-button type="button" x-on:click="confirmDeleteModal">Eliminar cliente</x-button>
                <x-secondary-button type="button" href="{{ route('client') }}"
                    wire:navigate>Cancelar</x-secondary-button>
            </div>
        </div>
        <!-- Force delete client -->
        <div class="py-8">
            <x-title-app>
                <x-slot name="title_page">Borrar cliente definitivamente</x-slot>
                <x-slot name="description_page">Elimina toda la información de un cliente permanentemente. Esto incluye
                    cuenta, notificaciones y convocatorias guardadas del cliente.</x-slot>
            </x-title-app>
            <div class="mb-4">
                <x-button type="button" x-on:click="confirmForceDeleteModal">Borrar cliente</x-button>
                <x-secondary-button type="button" href="{{ route('client') }}"
                    wire:navigate>Cancelar</x-secondary-button>
            </div>
        </div>
    </div>
    @script
        <script>
            Alpine.data('content', () => ({
                init() {
                    new TomSelect('#profesions').setValue($wire.form.profesion_id)
                },
                confirmUpdateModal() {
                    Swal.fire({
                        title: "¿Estás seguro?",
                        text: "Se actualizará la información del cliente.",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        confirmButtonColor: '#ff420a',
                        denyButtonText: "No",
                        denyButtonColor: '#484848'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.update()
                        }
                    });
                },
                confirmDeleteModal() {
                    Swal.fire({
                        title: "¿Eliminar cliente?",
                        text: "Se conservarán los datos relacionados con el cliente.",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        confirmButtonColor: '#ff420a',
                        denyButtonText: "No",
                        denyButtonColor: '#484848'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.delete()
                        }
                    });
                },
                confirmForceDeleteModal() {
                    Swal.fire({
                        title: "¿Borrar cliente?",
                        text: "Todos los datos del cliente serán borrados definitívamente.",
                        showDenyButton: true,
                        confirmButtonText: "Si",
                        confirmButtonColor: '#ff420a',
                        denyButtonText: "No",
                        denyButtonColor: '#484848'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.forceDelete()
                        }
                    });
                }
            }))
        </script>
    @endscript
</section>
