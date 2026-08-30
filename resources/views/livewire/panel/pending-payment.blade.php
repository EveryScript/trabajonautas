<section class="flex items-center justify-center min-h-screen py-10" wire:poll.7s="checkPayment">
    <div class="w-full max-w-md mx-4">
        <div class="p-8 bg-white rounded-lg shadow-lg dark:bg-tbn-dark">

            <div class="mb-6 max-w-40">
                <x-application-logo />
            </div>

            <h3 class="mb-1 text-lg font-semibold dark:text-white">
                Completa tu pago
            </h3>
            <p class="mb-6 text-sm text-tbn-secondary dark:text-tbn-light">
                Escanea el código QR con tu app bancaria para activar
                tu cuenta <span class="font-semibold text-tbn-primary">{{ $account_name }}</span>.
            </p>
            <!-- Flag QR Baneco / Default -->
            @if ($is_dynamic_qr && $qr_image)
                <div class="mb-2 text-center">
                    <span class="inline-block px-3 py-1 text-xs font-light text-tbn-primary">
                        <i class="mr-2 fa-regular fa-circle-dot animate-ping"></i> Esperando tu pago
                    </span>
                </div>
                <picture class="block max-w-[10rem] mx-auto mb-4">
                    <img class="w-full rounded-lg" src="data:image/png;base64,{{ $qr_image }}"
                        alt="Código QR de pago">
                </picture>
                <div class="mb-4 text-center">
                    <a href="data:image/png;base64,{{ $qr_image }}" download="qr-pago-trabajonautas.png"
                        class="inline-block px-3 py-2 text-xs transition-all duration-200 border rounded-full text-tbn-primary border-tbn-primary hover:bg-tbn-primary hover:text-white">
                        Descargar QR
                    </a>
                </div>
                @if ($qr_expires_at)
                    <p class="mb-6 text-xs text-center text-tbn-secondary dark:text-tbn-light">
                        Este QR vence el <span class="font-semibold">{{ $qr_expires_at }}</span>
                    </p>
                @endif
            @elseif ($qr_static)
                <div class="mb-2 text-center">
                    <span class="inline-block px-3 py-1 text-xs font-light text-tbn-secondary dark:text-tbn-light">
                        <i class="mr-2 fa-regular fa-circle-dot animate-ping"></i> Esperando tu pago
                    </span>
                </div>
                <picture class="block max-w-[10rem] mx-auto mb-4">
                    <img class="w-full rounded-lg" src="{{ asset('storage/' . $qr_static) }}" alt="Código QR de pago">
                </picture>
                <div class="mb-4 text-center">
                    <a href="{{ asset('storage/' . $qr_static) }}" download="qr-pago-trabajonautas.png"
                        class="inline-block px-3 py-2 text-xs transition-all duration-200 border rounded-full text-tbn-primary border-tbn-primary hover:bg-tbn-primary hover:text-white">
                        Descargar QR
                    </a>
                    <a href="https://wa.me/{{ env('SUPPORT_PHONE') }}?text=Hola%20Trabajonautas.com,%20he%20realizado%20el%20pago%20de%20mi%20cuenta%20{{ $this->account_name }}%20por%20QR,%20adjunto%20mi%20comprobante%20de%20pago%20(FOTO),%20para%20su%20verificación.%20Mi%20nombre%20es%20{{ $this->client_name }}%20y%20mi%20correo%20electrónico%20es%20{{ $this->client_email }}."
                        target="_blank"
                        class="inline-block px-3 py-2 text-xs transition-all duration-200 border rounded-full text-tbn-primary border-tbn-primary hover:bg-tbn-primary hover:text-white">
                        <i class="mr-1 fab fa-whatsapp"></i> Ya pagué el QR
                    </a>
                </div>
            @endif

            <div
                class="flex items-center justify-between p-4 mb-6 border rounded-xl border-tbn-light dark:border-tbn-secondary">
                <span class="text-sm text-tbn-secondary dark:text-tbn-light">Total a pagar</span>
                <span class="text-xl font-black text-tbn-dark dark:text-white">
                    {{ $amount }} Bs.
                </span>
            </div>
            <!-- Flag QR  Baneco / Default -->
            @if ($is_dynamic_qr && $qr_image)
                <p class="text-xs text-tbn-dark dark:text-tbn-light">
                    Tu cuenta se activará <span class="text-tbn-primary">automáticamente</span> en segundos una vez
                    confirmado el pago. No cierres esta página.
                </p>
            @else
                <p class="text-xs text-tbn-dark dark:text-tbn-light">
                    Ten paciencia por favor. Una vez realizado el depósito nuestros operadores <span
                        class="text-tbn-primary">confirmarán tu pago</span> y
                    habilitarán tu cuenta.
                </p>
            @endif
            <!-- Cancel payment-->
            <div class="mt-6">
                <button wire:click="repent" wire:loading.attr="disabled"
                    wire:confirm="¿Estás seguro? Tu QR será eliminado permanentemente."
                    class="w-full px-4 py-3 text-sm font-medium text-red-600 transition-colors duration-200 bg-red-600 border border-red-600 rounded-xl dark:text-white dark:border-tbn-primary hover:border-tbn-primary hover:text-tbn-primary dark:hover:border-tbn-light dark:hover:text-tbn-light">
                    <span wire:loading.remove wire:target="repent" class="uppercase">
                        Cancelar compra
                    </span>
                    <span wire:loading wire:target="repent" class="flex items-center justify-center gap-2 uppercase">
                        <i class="mr-1 text-sm fas fa-spinner animate-spin"></i> Cancelando QR...
                    </span>
                </button>
            </div>
        </div>
    </div>
</section>
