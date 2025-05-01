<section>
    <x-title-app>
        <x-slot name="title_page">Configuración de usuarios</x-slot>
        <x-slot name="description_page">
            Administra la información del usuario para permitirle el acceso de las funciones de Trabajonautas.
        </x-slot>
    </x-title-app>
    <form class="bg-white rounded-lg shadow-md px-10 py-8 max-w-3xl" x-data="content">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Nombre del cliente</span>
                <h4 class="text-lg font-medium">{{ $user->name }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Correo electrónico</span>
                <h4 class="text-lg font-medium">{{ $user->email }}</h4>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Profesion(es)</span>
                <ul>
                    @forelse ($user->myProfesions as $profesion)
                        <li class="inline text-md font-medium">
                            <h4 class="">{{ $profesion->profesion_name }}</h4>
                        </li>
                    @empty
                        <li class="text-tbn-dark italic text-sm">(vacio)</li>
                    @endforelse
                </ul>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Area profesional</span>
                @if ($user->area)
                    <h4 class="text-md font-medium">
                        {{ $user->area->area_name }}</h4>
                @else
                    <span class="text-tbn-dark block italic text-sm">(vacio)</span>
                @endif
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Fecha de registro</span>
                <h4 class="text-md font-medium">{{ $this->formatDate($user->created_at) }}</h4>
            </div>
        </div>
        <span class="text-xs text-tbn-primary">Verificación de pago</span>
        <div class="px-4 py-3 border border-tbn-primary rounded-lg mb-4 mt-1">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="user_verified_payment" class="sr-only peer" id="verification"
                    {{ $user->account->verified_payment ? 'checked' : '' }}>
                <div
                    class="relative w-14 h-7 bg-gray-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-tbn-primary">
                </div>
                <div class="ms-3">
                    <p class="text-md font-medium text-black">Verificación de pago</p>
                    <span class="text-xs text-tbn-dark">El cliente ha realizado el pago por una cuenta PRO.</span>
                </div>
            </label>
        </div>
        <div class="mb-4">
            <x-button type="button" @click="dangerModal">Guardar configuración</x-button>
        </div>
    </form>
    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets

    @script
        <script>
            Alpine.data('content', () => ({
                dangerFlag: false,
                userName: @json($user->name),
                userAccount: '',
                dangerModal() {
                    Swal.fire({
                        title: "Atención",
                        html: "¿Está seguro de guardar la configuración del usuario <strong>" + this
                            .userName + "</strong>",
                        showDenyButton: true,
                        confirmButtonText: "Guardar",
                        denyButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.save()
                        }
                    });
                }
            }))
        </script>
    @endscript
</section>
