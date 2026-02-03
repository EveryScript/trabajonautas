@props([
    'qr_image' => null,
])
<div x-show="step === 6" x-cloak x-transition:enter.duration.300ms>
    <h5 class="mb-1 font-bold text-md dark:text-white">Resumen de la compra</h5>
    <span class="block mb-4 text-xs text-tbn-dark dark:text-tbn-light">
        Revisa tus datos y escanea el código QR para realizar tu depósito.</span>
    <div class="flex flex-col w-full gap-6 my-4 md:flex-row">
        <div class="w-full text-sm md:w-3/5">
            <!-- Purchase review -->
            <section
                class="max-w-2xl mx-auto mb-4 overflow-hidden bg-white border shadow-sm dark:bg-tbn-dark rounded-2xl border-tbn-light dark:border-tbn-secondary">
                <div class="px-6 py-4">
                    <table class="w-full border-collapse">
                        <tbody class="divide-y divide-tbn-light dark:divide-tbn-secondary">
                            <tr>
                                <td class="py-2 pr-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-tbn-secondary dark:text-tbn-light mt-0.5">Nombre del
                                            cliente</span>
                                        <span class="font-normal text-md text-tbn-dark dark:text-white"
                                            x-text="user.name"></span>
                                    </div>
                                </td>
                                <td class="py-2 text-right align-top">
                                    <span class="text-lg font-bold text-tbn-dark dark:text-white"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm text-tbn-secondary dark:text-tbn-light mt-0.5">Ubicación</span>
                                        <span class="font-normal text-md text-tbn-dark dark:text-white"
                                            x-text="location_name"></span>
                                    </div>
                                </td>
                                <td class="py-2 text-right align-top">
                                    <span class="text-lg font-bold text-tbn-dark dark:text-white"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm text-tbn-secondary dark:text-tbn-light mt-0.5">Profesión</span>
                                        <span class="font-normal text-md text-tbn-dark dark:text-white"
                                            x-text="profesion_name"></span>
                                    </div>
                                </td>
                                <td class="py-2 text-right align-top">
                                    <span class="text-lg font-bold text-tbn-dark dark:text-white"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm text-tbn-secondary dark:text-tbn-light mt-0.5">Celular</span>
                                        <span class="font-normal text-md text-tbn-dark dark:text-white"
                                            x-text="user.phone"></span>
                                    </div>
                                </td>
                                <td class="py-2 text-right align-top">
                                    <span class="text-lg font-bold text-tbn-dark dark:text-white"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-tbn-secondary dark:text-tbn-light mt-0.5">
                                            Tipo de cuenta</span>
                                        <span class="font-semibold text-md text-tbn-primary"
                                            x-text="user.account_name"></span>
                                    </div>
                                </td>
                                <td class="py-2 text-right align-top">
                                    <span class="text-lg font-bold text-tbn-dark dark:text-white"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm text-tbn-secondary dark:text-tbn-light mt-0.5">Duración</span>
                                        <span class="font-normal text-md text-tbn-dark dark:text-white"
                                            x-text="user.account_duration +' días'"></span>
                                    </div>
                                </td>
                                <td class="py-2 text-right align-top">
                                    <span class="text-lg font-bold text-tbn-dark dark:text-white"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="px-8 py-6 space-y-3 bg-white border-t dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-extrabold text-tbn-primary">Total a pagar</span>
                        <span class="text-xl font-black text-tbn-dark dark:text-white"
                            x-text="user.account_price +' Bs.'"></span>
                    </div>
                </div>
            </section>

        </div>
        <!-- QR and Bank account -->
        <div class="w-full mb-4 text-sm md:w-2/5">
            <picture class="block max-w-[10rem] mx-auto mb-4">
                <img class="w-full rounded-lg" src="{{ asset('storage/' . $qr_image->value) }}" alt="qr-code">
            </picture>
            <div class="mb-8 text-center">
                <a href="{{ asset('storage/' . $qr_image->value) }}" download
                    class="inline-block px-3 py-2 text-xs transition-all duration-200 border rounded-full text-tbn-primary border-tbn-primary hover:bg-tbn-primary hover:text-white">
                    Descargar QR</a>
            </div>
            <div
                class="flex items-center justify-between w-full p-4 mb-6 transition-colors bg-white border shadow-sm md:max-w-sm dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary rounded-xl">
                <div class="flex items-center gap-4">
                    <div>
                        <h4 class="text-sm font-semibold text-tbn-secondary dark:text-tbn-light">
                            Banco Mercantil Santa Cruz</h4>
                        <p x-text="bankAccount" class="font-mono text-xl tracking-wider text-tbn-dark dark:text-white">
                        </p>
                    </div>
                </div>
                <button x-on:click="copyClipboardBankAccount"
                    class="p-2 rounded-full hover:text-tbn-primary text-tbn-secondary">
                    <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    <svg x-show="copied" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-tbn-dark dark:text-tbn-light">
                Una vez realizado el depósito nuestros operadores se comunicarán contigo para confirmar el
                depósito y habilitar tu cuenta.</p>
        </div>
    </div>
    <div class="flex justify-between mt-4">
        <x-secondary-button type="button" x-on:click="step = 5">
            Anterior</x-secondary-button>
        <x-button type="button" wire:click='confirmAndSave'>
            <span wire:loading.remove>Finalizar</span>
            <span wire:loading><i class="text-sm fas fa-spinner animate-spin"></i></span>
        </x-button>
    </div>
</div>
