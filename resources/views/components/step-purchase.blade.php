<div x-show="step === 6" x-cloak x-transition:enter.duration.300ms>
    <h5 class="mb-1 font-bold text-md dark:text-white">Resumen de la compra</h5>
    <span class="block mb-4 text-xs text-tbn-dark dark:text-tbn-light">
        Revisa tus datos y escanea el código QR para realizar tu depósito.</span>
    <div class="flex flex-col w-full gap-6 my-4 md:flex-row">
        <div class="w-full text-sm">
            <!-- Purchase review -->
            <section
                class="mx-auto mb-4 overflow-hidden bg-white border shadow-sm dark:bg-tbn-dark rounded-2xl border-tbn-light dark:border-tbn-secondary">
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
    </div>
    <div class="flex justify-between mt-4">
        <x-secondary-button type="button" x-on:click="step = 5">
            Anterior</x-secondary-button>
        <x-button wire:loading.attr='disabled' wire:target='confirmAndSave' type="button" wire:click='confirmAndSave'>
            <span wire:loading.remove>Generar QR</span>
            <span wire:loading>Generando...</span>
        </x-button>
    </div>
</div>
