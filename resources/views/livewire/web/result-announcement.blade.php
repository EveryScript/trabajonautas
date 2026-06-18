<div class="flex flex-col gap-6 px-4 md:flex-row">
    @php
        $unlock_flag =
            isset($client->account) &&
            $this->announcement->pro &&
            !$this->announcement->profesions->contains($client->profesion_id) &&
            !$client->unlockedAnnounces()->where('announcement_id', $this->announcement->id)->exists();
    @endphp
    <section class="w-full select-none md:w-3/5">
        @if ($unlock_flag)
            @if ($client->account->account_type_id == 3)
                <x-unlock :coins="$coins" />
            @else
                <x-restricted />
            @endif
        @else
            <x-result :announcement="$announcement" :total_locations="$total_locations" :client="$client" />
        @endif
    </section>
    <section class="w-full md:w-2/5">
        <!-- Company info -->
        <div class="mb-4">
            <h3 class="mb-1 font-medium text-tbn-dark dark:text-white text-md">Información de la empresa</h3>
            <div
                class="p-5 bg-white border rounded-lg shadow-md dark:bg-tbn-dark border-tbn-light dark:border-tbn-secondary">
                @if ($announcement->company)
                    <picture class="block mb-2">
                        <img alt="company-logo"
                            class="flex-shrink-0 object-cover object-center w-12 h-12 mb-4 rounded-lg sm:mb-0"
                            src="{{ asset('storage/' . $announcement->company->company_image) }}">
                    </picture>
                    <h5
                        class="inline font-bold mb-2 {{ $announcement->company->trashed() ? 'line-through opacity-40 ' : 'text-tbn-primary' }}">
                        {{ $announcement->company->company_name }}
                    </h5>
                    <span
                        class="inline-block px-2 py-[2px] -translate-y-1 text-xs rounded-lg bg-tbn-light dark:bg-neutral-900 text-white dark:text-tbn-light">
                        Empresa: {{ $announcement->company->companyType->company_type_name }}</span>
                    <p class="text-sm text-tbn-dark dark:text-white">
                        {{ $announcement->company->description }}</p>
                @else
                    <p class="text-sm italic text-tbn-dark dark:text-tbn-light">La empresa no tiene información
                        disponible</p>
                @endif

            </div>
        </div>
    </section>
</div>
