<section>
    <x-title-app>
        <x-slot name="title_page">Configuración del cliente</x-slot>
        <x-slot name="description_page">
            Realiza acciones para controlar el estado de un cliente.</x-slot>
    </x-title-app>
    <form class="bg-white dark:bg-tbn-dark rounded-lg shadow-md px-10 py-8 max-w-3xl" x-data="content">
        <div class="block md:grid grid-cols-2 gap-4 mb-4 dark:text-white">
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Nombre del cliente</span>
                <h4 class="text-lg font-medium">{{ $client->name }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Ubicación</span>
                <h4 class="text-lg font-medium">{{ $client->location->location_name }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Correo electrónico</span>
                <h4 class="text-lg font-medium">{{ $client->email }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Profesion</span>
                <h4 class="text-lg font-medium">{{ $client->profesion->profesion_name }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Celular</span>
                <h4 class="text-lg font-medium">{{ $client->phone }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Area profesional</span>
                @if ($client->area)
                    <h4 class="text-md font-medium">
                        {{ $client->area->area_name }}</h4>
                @else
                    <span class="text-tbn-dark block italic text-sm">(vacio)</span>
                @endif
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Fecha de registro</span>
                <h4 class="text-md font-medium">{{ $this->formatDate($client->created_at) }}</h4>
            </div>
            <div class="col-span-2">
                @if ($client->account->accountType->id !== 1)
                    <span class="text-xs text-tbn-primary">Verificación de pago</span>
                    <x-input-checkbox-block checked="{{ $client->account->verified_payment ? 'checked' : '' }}"
                        wire:model="client_verified_payment">
                        <div class="ms-4">
                            <p class="text-md font-medium text-tbn-dark dark:text-white">Verificación de pago</p>
                            <p class="text-xs text-tbn-dark dark:text-tbn-light">
                                El cliente ha realizado el pago de <span class="text-tbn-primary font-bold">
                                    {{ $client->account->accountType->price }} Bs. </span>
                                por cuenta <span class="text-tbn-primary font-bold">
                                    {{ $client->account->accountType->name }}</span> correctamente.</p>
                        </div>
                    </x-input-checkbox-block>
                @endif
                <span class="text-xs text-tbn-primary">Control de acceso</span>
                <x-input-checkbox-block checked="{{ $client->actived ? 'checked' : '' }}"
                    wire:model="client_actived">
                    <div class="ms-4">
                        <p class="text-md font-medium text-tbn-dark dark:text-white">Habilitar cliente</p>
                        <p class="text-xs text-tbn-dark dark:text-tbn-light">
                            El cliente utiliza el sistema y su cuenta está disponible actualmente </p>
                    </div>
                </x-input-checkbox-block>
            </div>
        </div>
        <div class="mb-4">
            <x-button type="button" x-on:click="saveSettings">Guardar configuración</x-button>
            <x-secondary-button href="{{ route('client') }}" wire:navigate>Salir</x-button>
        </div>
    </form>
    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                dangerFlag: false,
                userName: @json($client->name),
                userPhone: @json($client->phone),
                saveSettings() {
                    $wire.save().then(() => {
                        Swal.fire({
                            title: "Guardado",
                            html: "Los datos del cliente <strong>" +
                                this.userName + "</strong> se han guardado correctamente.",
                            showDenyButton: true,
                            confirmButtonColor: '#ff420a',
                            confirmButtonText: "Enviar Whatsapp",
                            denyButtonColor: '#484848',
                            denyButtonText: "Salir",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let phone = this.userPhone.replace(/[\s()+-]/g, '')
                                let url = 'https://api.whatsapp.com/send?phone=591' + phone +
                                    '&text=*Trabajonautas.com*%20te%20informa%20que%20tu%20cuenta%20ya%20está%20disponible.%20Ingresa%20a%20trabajonautas.com%2Fpanel%20ahora%20mismo'
                                window.open(url, '_blank')
                            }
                            if (result.isDenied)
                                $wire.justExit()
                        });
                    })
                }
            }))
        </script>
    @endscript
</section>
