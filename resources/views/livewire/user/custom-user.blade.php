<section>
    <x-title-app>
        <x-slot name="title_page">Configuración de usuarios</x-slot>
        <x-slot name="description_page">
            Administra la información del usuario para permitirle el acceso de las funciones de Trabajonautas.
        </x-slot>
    </x-title-app>
    <form class="max-w-3xl" x-data="content">
        <div class="grid grid-cols-2 gap-4">
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
                        <li><i class="fas fa-suitcase text-tbn-primary pr-1"></i>
                            <h4 class="inline text-md font-medium">{{ $profesion->profesion_name }}</h4>
                        </li>
                    @empty
                        <li class="text-tbn-dark italic text-sm">(vacio)</li>
                    @endforelse
                </ul>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Areas profesionales</span>
                <ul>
                    @forelse ($user->myAreas as $area)
                        <li><i class="fas fa-dot-circle text-tbn-primary pr-1"></i>
                            <h4 class="inline text-md font-medium">{{ $area->area_name }}</h4>
                        </li>
                    @empty
                        <li class="text-tbn-dark italic text-sm">(vacio)</li>
                    @endforelse
                </ul>
            </div>
            <div class="mb-1">
                <span class="text-xs text-tbn-primary">Fecha de registro</span>
                <h4 class="text-md font-medium">{{ $this->formatDate($user->created_at) }}</h4>
            </div>
        </div>
        <!-- Change type Account -->
        <div class="mb-4">
            <span class="text-xs text-tbn-primary">Tipo de cuenta</span>
            <ul class="grid w-full gap-4 grid-cols-2">
                <li>
                    <input @click="userAccount = '{{ FREE }}'" wire:model='user_account' type="radio"
                        id="free-account" class="hidden peer" value="{{ FREE }}"
                        {{ $user->hasRole(FREE) ? 'checked' : '' }}>
                    <label @click="" for="free-account"
                        class="inline-flex items-center justify-between w-full p-5 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                        <div class="w-2/3">
                            <div class="w-full text-lg font-semibold">Cuenta <span class="font-medium">FREE</span></div>
                            <div class="w-full text-sm">El cliente tiene privilegios limitados de búsqueda y
                                notificación.</div>
                        </div>
                        <i class="fas fa-leaf text-green-500 text-2xl"></i>
                    </label>
                </li>
                <li>
                    <input @click="userAccount = '{{ PRO }}'" wire:model='user_account' type="radio"
                        id="pro-account" class="hidden peer" value="{{ PRO }}"
                        {{ $user->hasRole(PRO) ? 'checked' : '' }} />
                    <label for="pro-account"
                        class="inline-flex items-center justify-between w-full p-5 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                        <div class="w-2/3">
                            <div class="w-full text-lg font-semibold">Cuenta <span class="font-medium">PRO</span></div>
                            <div class="w-full text-sm">El cliente tiene todos los privilegios de búsqueda y
                                notificación.</div>
                        </div>
                        <i class="fas fa-crown text-orange-500 text-2xl"></i>
                    </label>
                </li>
            </ul>
        </div>
        <!-- Change access to system -->
        <div class="mb-4">
            <span class="text-xs text-tbn-primary">Privilegios de usuarios</span>
            <ul class="grid w-full gap-4 md:grid-cols-2">
                <li>
                    <input @click="userAccount = '{{ USER }}'" wire:model='user_account' type="radio"
                        id="user-account" class="hidden peer" value="{{ USER }}"
                        {{ $user->hasRole(USER) ? 'checked' : '' }}>
                    <label @click="dangerFlag = true" for="user-account"
                        class="inline-flex items-center justify-between w-full p-5 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                        <div class="w-2/3">
                            <div class="w-full text-lg font-semibold">Usuario del sistema</div>
                            <div class="w-full text-sm">El usuario puede administrar la información básica del sistema
                                Trabajonautas.</div>
                        </div>
                        <i class="fas fa-user text-tbn-primary text-2xl"></i>
                    </label>
                </li>
                <li>
                    <input @click="userAccount = '{{ ADMIN }}'" wire:model='user_account' type="radio"
                        id="admin-account" class="hidden peer" value="{{ ADMIN }}"
                        {{ $user->hasRole(ADMIN) ? 'checked' : '' }} />
                    <label @click="dangerFlag = true" for="admin-account"
                        class="inline-flex items-center justify-between w-full p-5 text-gray-700 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:text-gray-600 hover:bg-gray-100">
                        <div class="w-2/3">
                            <div class="w-full text-lg font-semibold">Administrador del sistema</div>
                            <div class="w-full text-sm">El usuario puede administrar toda la información del sistema
                                trabajonautas sin restricciones;</div>
                        </div>
                        <i class="fas fa-user-cog text-tbn-dark text-2xl"></i>
                    </label>
                </li>
            </ul>
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
                    console.log(this.userAccount)
                    Swal.fire({
                        title: "Atención",
                        html: "El usuario " + this.userName + " tendrá privilegios de <strong>" +
                            this.setUserRole(this.userAccount) +
                            "</strong>. ¿Está seguro de guardar la configuración",
                        showDenyButton: true,
                        confirmButtonText: "Confirmar",
                        denyButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.save()
                        }
                    });
                },
                setUserRole(role) {
                    switch (role) {
                        case 'ADMIN':
                            return 'Administrador';
                            break;
                        case 'USER':
                            return 'Usuario';
                            break;
                        case 'FREE_CLIENT':
                            return 'Cliente (FREE)';
                            break;
                        case 'PRO_CLIENT':
                            return 'Cliente (PRO)';
                            break;
                    }
                },
            }))
        </script>
    @endscript
</section>
