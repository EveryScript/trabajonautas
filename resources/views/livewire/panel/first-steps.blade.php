<section class="flex items-start justify-center min-h-screen py-10">
    <div x-data="content" class="w-full max-w-xl md:max-w-4xl">
        <div class="p-6 mx-2 bg-white rounded-lg shadow-lg md:p-10 dark:bg-tbn-dark">
            <div class="mb-3 max-w-60">
                <x-application-logo />
            </div>
            <h3 class="mb-1 text-lg font-semibold dark:text-white md:text-xl">
                Hola {{ auth()->user()->name }}</h3>
            <p class="mb-4 text-sm text-tbn-secondary dark:text-tbn-light">Estamos listos para despegar contigo. Ingresa
                tu información para
                completar tu registro.</p>
            <!-- Step 1 : Gender, Age, Phone -->
            <x-step-personal />
            <!-- Step 2 : Grade profile -->
            <x-step-grade />
            <!-- Step 3 : Profesion -->
            <x-step-profesional />
            <!-- Step 4 : Locations -->
            <x-step-location />
            <!-- Step 5 : Select your account -->
            <x-step-account />
            <!-- Step 6 : Purchase review -->
            <x-step-purchase :qr_image="$qr_image" />
        </div>
    </div>
    @assets
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets
    @script
        <script>
            Alpine.data('content', () => ({
                // Models
                step: 1,
                searchProfesion: '',
                location_name: '',
                profesion_name: '',
                url_whatsapp: '',
                // Propeties
                gender: @entangle('form.gender'),
                age: @entangle('form.age'),
                phone: @entangle('form.phone'),
                grade_profile_id: @entangle('form.grade_profile_id'),
                profesion_id: @entangle('form.profesion_id'),
                location_id: @entangle('form.location_id'),
                account_type_id: @entangle('form.account_type_id'),
                account_price: @entangle('form.account_price'),
                // Data
                user: @json($user),
                profesions: @json($profesions),
                locations: @json($locations),
                accountTypes: @json($account_types),
                // Bank Account
                bankAccount: '4077070681',
                copied: false,
                // Functions
                init() {
                    $wire.on('register-failed', () => {
                        Swal.fire({
                            title: "Error",
                            text: 'Ha ocurrido un error al guardar los datos del cliente. Intentelo más tarde',
                            confirmButtonColor: '#ff420a'
                        })
                    })
                },
                setAccountData(accountType) {
                    this.account_type_id = accountType.id
                    this.account_price = accountType.price
                },
                isProAccountSelected() {
                    if (this.account_type_id == 2 || this.account_type_id == 3) {
                        this.step = 6
                        this.accountTypes.find(account => {
                            if (account.id == this.account_type_id) {
                                this.user.phone = this.phone
                                this.user.account_name = account.name
                                this.user.account_price = account.price
                                this.user.account_duration = account.duration_days
                            }
                        })
                    } else {
                        $wire.confirmAndSave()
                    }
                },
                isValidPhone() {
                    this.url_whatsapp = 'https://wa.me/591' + this.phone
                    return /^[67]\d{7}$/.test(this.phone)
                },
                filteredProfesions() {
                    return this.profesions.filter(
                        profesion => profesion.profesion_name.toLowerCase().includes(this.searchProfesion
                            .toLowerCase())
                    )
                },
                setLocation(id) {
                    this.location_id = id
                    this.location_name = this.locations.find(location => location.id == id).location_name
                },
                setProfesion(id) {
                    this.profesion_id = id
                    this.profesion_name = this.profesions.find(profesion => profesion.id == id).profesion_name
                },
                verifyWhatsappNumber() {
                    window.open(this.url_whatsapp)
                },
                copyClipboardBankAccount() {
                    navigator.clipboard.writeText(this.bankAccount)
                    this.copied = true
                    setTimeout(() => this.copied = false, 2000);
                }
            }))
        </script>
    @endscript
</section>
