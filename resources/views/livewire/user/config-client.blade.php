<section>
    <x-title-app>
        <x-slot name="title_page">Configuración del cliente</x-slot>
        <x-slot name="description_page">
            Realiza acciones para controlar el estado de un cliente.</x-slot>
    </x-title-app>
    <form class="bg-white rounded-lg shadow-md px-10 py-8 max-w-3xl" x-data="content">
        <div class="grid grid-cols-2 gap-4 mb-4">
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
                <span class="text-xs text-tbn-primary">Profesion(es)</span>
                <ul>
                    @forelse ($client->myProfesions as $profesion)
                        <li class="inline text-md font-medium">
                            <h4 class="">{{ $profesion->profesion_name }}</h4>
                        </li>
                    @empty
                        <li class="text-tbn-dark italic text-sm">(vacio)</li>
                    @endforelse
                </ul>
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
            @if ($client->account->accountType->id !== 1)
                <div class="col-span-2">
                    <span class="text-xs text-tbn-primary">Verificación de pago</span>
                    <div class="px-4 py-3 border border-tbn-primary rounded-lg mb-4 mt-1">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="client_verified_payment" class="sr-only peer"
                                id="verification" {{ $client->account->verified_payment ? 'checked' : '' }}>
                            <div
                                class="relative w-14 h-7 bg-gray-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-tbn-primary">
                            </div>
                            <div class="ms-3">
                                <p class="text-md font-medium text-black">Verificación de pago</p>
                                <p class="text-xs text-tbn-dark">
                                    El cliente ha realizado el pago de <span class="text-tbn-primary font-bold">
                                        {{ $client->account->accountType->price }} Bs. </span>
                                    por cuenta <span class="text-tbn-primary font-bold">
                                        {{ $client->account->accountType->name }}</span> correctamente.</p>
                            </div>
                        </label>
                    </div>
                </div>
            @endif
            <div class="col-span-2">
                <span class="text-xs text-red-700">Control de acceso</span>
                <div class="px-4 py-3 border bg-white border-red-700 rounded-lg mb-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" id="actived" wire:model="client_actived"
                            {{ $client->actived ? 'checked' : '' }}>
                        <div
                            class="relative w-14 h-7 bg-gray-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-tbn-primary">
                        </div>
                        <div class="ms-3">
                            <p class="text-md font-medium text-red-700">Habilitar cliente</p>
                            <span class="text-xs text-tbn-dark">
                                El cliente utiliza el sistema y su cuenta está disponible actualmente </span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <x-button type="button" @click="saveSettings">Guardar configuración</x-button>
            {{-- <x-button type="button" @click="dangerModal">Guardar configuración</x-button> --}}
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
                            confirmButtonColor: '#034b8d',
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
