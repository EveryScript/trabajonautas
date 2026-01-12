<section>
    <x-title-app>
        <x-slot name="title_page">Configuración del cliente</x-slot>
        <x-slot name="description_page">
            Realiza acciones para controlar el estado de un cliente.</x-slot>
    </x-title-app>
    <form class="max-w-3xl px-10 py-8 bg-white rounded-lg shadow-md dark:bg-tbn-dark" x-data="content">
        <div class="block grid-cols-2 gap-4 mb-4 md:grid dark:text-white">
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Nombre del cliente</span>
                <h4 class="text-lg font-medium">{{ $client->name }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Ubicación</span>
                <h4 class="text-lg font-medium">
                    {{ $client->location ? $client->location->location_name : '(sin datos)' }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Correo electrónico</span>
                <h4 class="text-lg font-medium">{{ $client->email }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Profesion</span>
                <h4 class="text-lg font-medium">
                    {{ $client->profesion ? $client->profesion->profesion_name : '(sin datos)' }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Celular</span>
                <h4 class="text-lg font-medium">
                    @if ($client->phone)
                        {{ substr($client->phone, 4, 10) }} <a href="http://wa.me/{{ $client->phone }}" target="_blank"
                            rel="phone-verify" class="text-green-500 underline">Verificar</a>
                    @else
                        (sin datos)
                    @endif
                </h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Fecha de registro</span>
                <h4 class="font-medium text-md">{{ $this->formatDate($client->created_at) }}</h4>
            </div>
            <div class="col-span-2">
                @if ($client->account && intval($client->account->account_type_id) !== 1)
                    <span class="text-xs text-tbn-primary">Verificación de pago</span>
                    <x-input-checkbox-block checked="{{ $client->account->verified_payment ? 'checked' : '' }}"
                        wire:model="client_verified_payment">
                        <div class="ms-4">
                            <p class="font-medium text-md text-tbn-dark dark:text-white">Verificación de pago</p>
                            <p class="text-xs text-tbn-dark dark:text-tbn-light">
                                El cliente ha realizado el pago de <span class="font-bold text-tbn-primary">
                                    {{ $client->account->accountType->price }} Bs. </span>
                                por cuenta <span class="font-bold text-tbn-primary">
                                    {{ $client->account->accountType->name }}</span> correctamente.</p>
                        </div>
                    </x-input-checkbox-block>
                @endif
                @if ($client->account)
                    <span class="text-xs text-tbn-primary">Control de acceso</span>
                    <x-input-checkbox-block checked="{{ $client->actived ? 'checked' : '' }}"
                        wire:model="client_actived">
                        <div class="ms-4">
                            <p class="font-medium text-md text-tbn-dark dark:text-white">Habilitar cliente</p>
                            <p class="text-xs text-tbn-dark dark:text-tbn-light">
                                El cliente utiliza el sistema y su cuenta está disponible actualmente </p>
                        </div>
                    </x-input-checkbox-block>
                @endif
            </div>
        </div>
        <div class="mb-4">
            @if ($client->account)
                <x-button type="button" x-on:click="saveSettings">
                    <span wire:loading.remove>Guardar configuración</span>
                    <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
                </x-button>
            @endif
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
                                let url = 'https://wa.me/591' + phone +
                                    '?text=*Trabajonautas.com*%20te%20informa%20que%20tu%20cuenta%20ya%20está%20disponible.%20Ingresa%20a%20trabajonautas.com/panel%20ahora%20mismo'
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
