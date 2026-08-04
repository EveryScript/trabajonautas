<section class="flex items-center justify-center min-h-screen py-10" wire:poll.5s="checkPayment">
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

            @if ($qr_image)
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
            @endif

            <div
                class="flex items-center justify-between p-4 mb-6 border rounded-xl border-tbn-light dark:border-tbn-secondary">
                <span class="text-sm text-tbn-secondary dark:text-tbn-light">Total a pagar</span>
                <span class="text-xl font-black text-tbn-dark dark:text-white">
                    {{ $amount }} Bs.
                </span>
            </div>

            <p class="text-xs text-tbn-dark dark:text-tbn-light">
                Tu cuenta se activará automáticamente en segundos una vez
                confirmado el pago. No cierres esta página.
            </p>

        </div>
    </div>
</section>
