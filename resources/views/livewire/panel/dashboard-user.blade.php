<section class="mt-4">
    <div class="bg-white rounded-md shadow-md px-7 py-5" x-data="content">
        <h4 class="text-2xl text-tbn-primary font-bold">Bienvenido {{ Auth::user()->name }}</h4>
        <p class="mb-5 text-tbn-dark text-sm">Esta es la actividad más reciente en Trabajonautas.com</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <!-- Clients by role chart -->
            <div class="col-span-3 md:col-span-1 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes registrados</h4>
                <p class="mb-5 text-tbn-dark text-sm">Cantidad total de clientes: {{ array_sum($tbn_clients['data']) }}
                </p>
                <canvas id="clients-chart"></canvas>
            </div>
            <!-- Announcements by user chart -->
            <div
                class="col-span-3 md:col-span-2 border border-gray-300 rounded-lg shadow-lg px-5 py-4 
                {{ Auth::user()->hasRole('USER') ? 'hidden' : '' }}">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Convocatorias registradas por usuario</h4>
                <p class="mb-5 text-tbn-dark text-sm">Cantidad total de convocatorias:
                    {{ array_sum($tbn_announces_user['data']) }}</p>
                <canvas id="announces-user-chart"></canvas>
            </div>
            <!-- Clients by age chart -->
            <div class="col-span-3 md:col-span-1 border border-gray-300 rounded-lg shadow-lg px-5 py-4"
                {{ Auth::user()->hasRole('USER') ? 'hidden' : '' }}>
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes según la edad </h4>
                <p class="mb-5 text-tbn-dark text-sm">Cantidad total de clientes: {{ array_sum($tbn_clients['data']) }}
                </p>
                <canvas id="users-age-chart"></canvas>
            </div>
            <!-- Top Users by location chart -->
            <div class="col-span-3 md:col-span-2 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes según ubicación</h4>
                <p class="mb-5 text-tbn-dark text-sm">Cantidad total de clientes:
                    {{ array_sum($tbn_users_location['data']) }}</p>
                <canvas id="users-location-chart"></canvas>
            </div>
            <!-- Top Users by profesion chart -->
            <div class="col-span-3 border border-gray-300 rounded-lg shadow-lg px-5 py-4"
                {{ Auth::user()->hasRole('USER') ? 'hidden' : '' }}>
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Top 10 de profesiones más registradas</h4>
                <canvas id="users-profesion-chart"></canvas>
            </div>
            <!-- Announcements by area chart -->
            <div class="col-span-3 border border-gray-300 rounded-lg shadow-lg px-5 py-4"
                {{ Auth::user()->hasRole('USER') ? 'hidden' : '' }}>
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Convocatorias registradas por area</h4>
                <p class="mb-5 text-tbn-dark text-sm">Cantidad total de convocatorias:
                    {{ array_sum($tbn_announces_area['data']) }}</p>
                <canvas id="announces-area-chart"></canvas>
            </div>
        </div>
    </div>
    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endassets
    @script
        <script>
            const clientsChart = document.getElementById('clients-chart')
            const announcesAreaChart = document.getElementById('announces-area-chart')
            const announcesUserChart = document.getElementById('announces-user-chart')
            const usersProfesionChart = document.getElementById('users-profesion-chart')
            const usersLocationChart = document.getElementById('users-location-chart')
            const usersAgeChart = document.getElementById('users-age-chart')

            Alpine.data('content', () => ({
                tbn_clients: @json($tbn_clients),
                tbn_announces_area: @json($tbn_announces_area),
                tbn_announces_user: @json($tbn_announces_user),
                tbn_users_profesion: @json($tbn_users_profesion),
                tbn_users_location: @json($tbn_users_location),
                tbn_users_age: @json($tbn_users_age),
                init() {
                    // Clients chart
                    new Chart(clientsChart, {
                        type: 'doughnut',
                        data: {
                            labels: this.tbn_clients.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_clients.data,
                                backgroundColor: this.tbn_clients.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Users by age chart
                    new Chart(usersAgeChart, {
                        type: 'doughnut',
                        data: {
                            labels: this.tbn_users_age.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_users_age.data,
                                backgroundColor: this.tbn_users_age.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Announces by area chart
                    new Chart(announcesAreaChart, {
                        type: 'bar',
                        data: {
                            labels: this.tbn_announces_area.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_announces_area.data,
                                backgroundColor: this.tbn_announces_area.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Announces by user chart
                    new Chart(announcesUserChart, {
                        type: 'bar',
                        data: {
                            labels: this.tbn_announces_user.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_announces_user.data,
                                backgroundColor: this.tbn_announces_user.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Users by profesion chart
                    new Chart(usersProfesionChart, {
                        type: 'bar',
                        data: {
                            labels: this.tbn_users_profesion.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_users_profesion.data,
                                backgroundColor: this.tbn_users_profesion.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Users by location chart
                    new Chart(usersLocationChart, {
                        type: 'bar',
                        data: {
                            labels: this.tbn_users_location.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_users_location.data,
                                backgroundColor: this.tbn_users_location.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                }
            }))
        </script>
    @endscript
</section>
