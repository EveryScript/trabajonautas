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
                    labels: initialData.labels,
                    datasets: [{
                        label: 'Cantidad',
                        data: initialData.data,
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
    class="border border-gray-300 rounded-lg shadow-lg px-5 py-4">
    <span class="text-xs text-tbn-secondary">{{ $labels['format_date'] }}</span>
    <h4 class="text-lg text-tbn-dark font-semibold mb-1">Clientes según grado académico </h4>
    <div class="flex flex-row">
        <p class="w-1/2 mb-5 text-tbn-dark text-sm">
            <span class="font-medium">Cantidad total: </span> {{ $total }}
        </p>
    </div>
    <div wire:ignore wire:loading.remove class="relative h-64 w-full">
        <canvas class="w-full max-h-[20rem]" x-ref="canvas"></canvas>
    </div>
    <div wire:loading class="relative h-64 w-full">
        <p class="absolute top-32 left-1/2 right-1/2 m-auto">
            <i class="fas fa-spinner text-xl text-tbn-secondary animate-spin"></i>
        </p>
    </div>
</div>
