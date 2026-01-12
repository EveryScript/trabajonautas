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
                    labels: [],
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
    class="px-5 py-4 border border-gray-300 rounded-lg shadow-lg dark:border-tbn-secondary">
    <span class="text-xs text-tbn-secondary dark:text-tbn-light">{{ $labels['format_date'] }}</span>
    <h4 class="mb-1 text-lg font-semibold text-tbn-dark dark:text-tbn-primary">Clientes sin datos </h4>
    <div wire:ignore wire:loading.remove class="relative w-full h-64">
        <canvas class="w-full max-h-[20rem]" x-ref="canvas"></canvas>
    </div>
    <div wire:loading class="relative w-full h-64">
        <p class="absolute m-auto top-32 left-1/2 right-1/2">
            <i class="text-xl fas fa-spinner text-tbn-secondary dark:text-tbn-primary animate-spin"></i>
        </p>
    </div>
</div>