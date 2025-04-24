<label class="inline-flex items-center cursor-pointer">
    <input type="checkbox" wire:model="verified_payment" class="sr-only peer" id="verification" {{  }}>
    <div
        class="relative w-14 h-7 bg-gray-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-tbn-primary">
    </div>
    <div class="ms-3">
        <p class="text-md font-medium text-black">Verificación de pago</p>
        <span class="text-xs text-tbn-dark">El cliente ha realizado el pago por una cuenta PRO.</span>
    </div>
</label>
