<section class="mt-4">
    <div class="bg-white rounded-md shadow-md px-7 py-5" x-data="content">
        <h4 class="text-2xl text-tbn-primary font-bold">Bienvenido {{ Auth::user()->name }}</h4>
        <p class="mb-5 text-tbn-dark text-sm">Esta es la actividad más reciente en Trabajonautas.com</p>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
            <!-- Clients by role chart -->
            <div class="col-span-4 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes registrados</h4>
                <p class="mb-5 text-tbn-dark text-sm">
                    Cantidad total de clientes: {{ array_sum($tbn_clients['data']) }}</p>
                <canvas id="clients-chart"></canvas>
            </div>
            <!-- Clients by age chart -->
            <div class="col-span-4 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes según la edad </h4>
                <p class="mb-5 text-tbn-dark text-sm">
                    Cantidad total de clientes: {{ array_sum($tbn_clients['data']) }}</p>
                <canvas id="clients-age-chart"></canvas>
            </div>
            <!-- Clients by genere chart -->
            <div class="col-span-4 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes según género </h4>
                <p class="mb-5 text-tbn-dark text-sm">
                    Cantidad total de clientes: {{ array_sum($tbn_clients['data']) }}</p>
                <canvas id="clients-gender-chart"></canvas>
            </div>
            <!-- Top Users by grade profile chart -->
            <div class="col-span-6 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes según grado académico</h4>
                <p class="mb-5 text-tbn-dark text-sm">
                    Cantidad total de clientes: {{ array_sum($tbn_clients_grade['data']) }}</p>
                <canvas id="clients-grade-chart"></canvas>
            </div>
            <!-- Top Clients by area chart -->
            <div class="col-span-6 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes según área</h4>
                <p class="mb-5 text-tbn-dark text-sm">
                    Cantidad total de clientes: {{ array_sum($tbn_clients_area['data']) }}</p>
                <canvas id="clients-area-chart"></canvas>
            </div>
            <!-- Top Users by location chart -->
            <div class="col-span-12 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Clientes según ubicación</h4>
                <p class="mb-5 text-tbn-dark text-sm">
                    Cantidad total de clientes: {{ array_sum($tbn_clients_location['data']) }}</p>
                <canvas id="clients-location-chart"></canvas>
            </div>
            <!-- Announcements by area chart -->
            <div class="col-span-6 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Convocatorias según área</h4>
                <p class="mb-5 text-tbn-dark text-sm">
                    Cantidad total de convocatorias: {{ array_sum($tbn_announces_area['data']) }}</p>
                <canvas id="announces-area-chart"></canvas>
            </div>
            <!-- Announcements by user chart -->
            <div
                class="col-span-6 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Convocatorias por usuario</h4>
                <p class="mb-5 text-tbn-dark text-sm">
                    Cantidad total de convocatorias: {{ array_sum($tbn_announces_user['data']) }}</p>
                <canvas id="announces-user-chart"></canvas>
            </div>
            <!-- Top Users by profesion chart -->
            <div class="col-span-12 border border-gray-300 rounded-lg shadow-lg px-5 py-4">
                <h4 class="text-lg text-tbn-dark font-bold mb-1">Top 10 de profesiones más registradas</h4>
                <canvas id="clients-profesion-chart"></canvas>
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
            const clientsProfesionChart = document.getElementById('clients-profesion-chart')
            const clientsLocationChart = document.getElementById('clients-location-chart')
            const clientsAgeChart = document.getElementById('clients-age-chart')
            const clientsGenderChart = document.getElementById('clients-gender-chart')
            const clientsGradeChart = document.getElementById('clients-grade-chart')
            const clientsAreaChart = document.getElementById('clients-area-chart')

            Alpine.data('content', () => ({
                tbn_clients: @json($tbn_clients),
                tbn_announces_area: @json($tbn_announces_area),
                tbn_announces_user: @json($tbn_announces_user),
                tbn_clients_profesion: @json($tbn_clients_profesion),
                tbn_clients_location: @json($tbn_clients_location),
                tbn_clients_age: @json($tbn_clients_age),
                tbn_clients_gender: @json($tbn_clients_gender),
                tbn_clients_grade: @json($tbn_clients_grade),
                tbn_clients_area: @json($tbn_clients_area),

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
                    new Chart(clientsAgeChart, {
                        type: 'doughnut',
                        data: {
                            labels: this.tbn_clients_age.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_clients_age.data,
                                backgroundColor: this.tbn_clients_age.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Users by age chart
                    new Chart(clientsGenderChart, {
                        type: 'doughnut',
                        data: {
                            labels: this.tbn_clients_gender.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_clients_gender.data,
                                backgroundColor: this.tbn_clients_gender.backgroundColor,
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
                    new Chart(clientsProfesionChart, {
                        type: 'bar',
                        data: {
                            labels: this.tbn_clients_profesion.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_clients_profesion.data,
                                backgroundColor: this.tbn_clients_profesion.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Users by location chart
                    new Chart(clientsLocationChart, {
                        type: 'bar',
                        data: {
                            labels: this.tbn_clients_location.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_clients_location.data,
                                backgroundColor: this.tbn_clients_location.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Users by grade profile chart
                    new Chart(clientsGradeChart, {
                        type: 'bar',
                        data: {
                            labels: this.tbn_clients_grade.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_clients_grade.data,
                                backgroundColor: this.tbn_clients_grade.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                    // Clients by area chart
                    new Chart(clientsAreaChart, {
                        type: 'bar',
                        data: {
                            labels: this.tbn_clients_area.labels,
                            datasets: [{
                                label: 'Cantidad',
                                data: this.tbn_clients_area.data,
                                backgroundColor: this.tbn_clients_area.backgroundColor,
                                hoverOffset: 4
                            }]
                        }
                    })
                }
            }))
        </script>
    @endscript
</section>
