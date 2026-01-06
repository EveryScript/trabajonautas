<div x-data="{
    chart: null,
    async updateChart() {
        if (this.chart) {
            try { this.chart.destroy(); } catch (e) { console.error(e) }
            this.chart = null;
        }
        try {
            let initialData = await $wire.getChartData();
            let initialLabels = $wire.labels

            this.chart = new Chart(this.$refs.canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['De 18 a 25', 'De 26 a 32', 'De 33 en adelante'],
                    datasets: [{
                        label: 'Cantidad',
                        data: initialData,
                        backgroundColor: initialLabels.color,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        } catch (err) {
            console.error('Error updating chart: ', err)
        }
    }
}" x-init="updateChart()" @refresh-charts.window="updateChart()"
    class="border border-gray-300 dark:border-tbn-secondary rounded-lg shadow-lg px-5 py-4">
    <span class="text-xs text-tbn-secondary dark:text-tbn-light">{{ $labels['format_date'] }}</span>
    <h4 class="text-lg text-tbn-dark dark:text-tbn-primary font-semibold mb-1">Clientes según edad </h4>
    <div class="flex flex-row">
        <p class="w-1/2 mb-5 text-tbn-dark dark:text-tbn-light text-sm">
            <span class="font-medium">Cantidad total: </span> {{ $total }}
        </p>
    </div>
    <div wire:ignore wire:loading.remove class="relative h-64 w-full">
        <canvas class="w-full max-h-[20rem]" x-ref="canvas"></canvas>
    </div>
    <div wire:loading class="relative h-64 w-full">
        <p class="absolute top-32 left-1/2 right-1/2 m-auto">
            <i class="fas fa-spinner text-xl text-tbn-secondary dark:text-tbn-primary animate-spin"></i>
        </p>
    </div>
</div>
