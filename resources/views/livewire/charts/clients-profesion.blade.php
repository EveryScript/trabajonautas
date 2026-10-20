<div x-data="{
    chart: null,
    limit: 10, // Valor inicial por defecto (Top 10)
    allLabels: [],
    allData: [],

    async initChart() {
        try {
            let result = await $wire.getChartData();
            this.allLabels = result.labels;
            this.allData = result.data;
            this.renderChart();
        } catch (err) {
            console.error('Error fetching chart data: ', err);
        }
    },

    renderChart() {
        if (this.chart) {
            try { this.chart.destroy(); } catch (e) { console.error(e) }
            this.chart = null;
        }

        // Recortamos los datos localmente según el límite seleccionado en Alpine
        let slicedLabels = this.allLabels.slice(0, parseInt(this.limit));
        let slicedData = this.allData.slice(0, parseInt(this.limit));
        let initialLabelsConfig = @js($labels);

        this.chart = new Chart(this.$refs.canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: slicedLabels,
                datasets: [{
                    label: 'Cantidad',
                    data: slicedData,
                    backgroundColor: initialLabelsConfig.color,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });
    },

    changeLimit(newLimit) {
        this.limit = newLimit;
        this.renderChart(); // Redibuja al instante sin tocar PHP
    }
}" x-init="initChart()" @refresh-charts.window="initChart()"
    class="border border-gray-300 dark:border-tbn-secondary rounded-lg shadow-lg px-5 py-4">
    <span class="text-xs text-tbn-secondary dark:text-tbn-light">{{ $labels['format_date'] }}</span>
    <div class="flex justify-between items-center mb-2">
        <h4 class="text-lg text-tbn-dark dark:text-tbn-primary font-semibold">Clientes según profesión</h4>
        <div class="flex items-center space-x-2 text-xs text-tbn-dark dark:text-tbn-light">
            <label for="alpineLimit" class="font-medium">Mostrar:</label>
            <select x-model="limit" @change="renderChart()" id="alpineLimit" class="text-xs bg-white dark:bg-tbn-dark border border-gray-300 dark:border-tbn-secondary rounded px-2 py-1 focus:outline-none">
                <option value="10">Top 10</option>
                <option value="20">Top 20</option>
                <option value="30">Top 30</option>
                <option value="50">Top 50</option>
            </select>
        </div>
    </div>
    <div class="flex flex-row">
        <p class="w-1/2 mb-5 text-tbn-dark dark:text-tbn-light text-sm">
            @if ($labels['client_type'])
                <span class="font-medium {{ $filterClient == 1 ? 'text-green-600' : 'text-tbn-primary' }}">
                    <i class="mr-1 fas {{ $filterClient == 1 ? 'fa-leaf' : 'fa-crown' }}"></i>
                    {{ $labels['client_type'] }}:
                </span> {{ $total }}
            @else
                <span class="font-medium">Cantidad total: </span> {{ $total }}
            @endif
        </p>
    </div>
    <div wire:ignore wire:loading.remove class="relative h-64 w-full">
        <canvas class="w-full max-h-[20rem]" x-ref="canvas"></canvas>
    </div>
    <div wire:loading class="relative h-64 w-full flex items-center justify-center">
        <p>
            <i class="fas fa-spinner text-xl text-tbn-secondary dark:text-tbn-primary animate-spin"></i>
        </p>
    </div>
</div>
