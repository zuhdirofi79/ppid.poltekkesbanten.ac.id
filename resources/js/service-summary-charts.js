import Chart from 'chart.js/auto';

const node = document.getElementById('service-summary-chart-data');
if (node) {
    const data = JSON.parse(node.textContent || '{}');
    const green = '#008d70';
    const gold = '#d7ae62';
    const navy = '#17184f';

    const status = data.status?.segments ?? [];
    const statusCanvas = document.getElementById('chart-status');
    if (statusCanvas && status.length) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: status.map((segment) => segment.label),
                datasets: [{
                    data: status.map((segment) => segment.value),
                    backgroundColor: [green, gold, navy],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    }

    const volumeCanvas = document.getElementById('chart-volume');
    const volumeLabels = data.volume?.labels ?? [];
    if (volumeCanvas && volumeLabels.length) {
        new Chart(volumeCanvas, {
            type: 'bar',
            data: {
                labels: volumeLabels,
                datasets: [{
                    label: 'Permohonan diterima',
                    data: data.volume?.values ?? [],
                    backgroundColor: navy,
                    borderRadius: 6,
                    maxBarThickness: 48,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });
    }

    const reasonCanvas = document.getElementById('chart-reasons');
    const reasonLabels = data.reasons?.labels ?? [];
    if (reasonCanvas && reasonLabels.length) {
        new Chart(reasonCanvas, {
            type: 'bar',
            data: {
                labels: reasonLabels,
                datasets: [{
                    label: 'Jumlah',
                    data: data.reasons?.values ?? [],
                    backgroundColor: green,
                    borderRadius: 6,
                    maxBarThickness: 28,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });
    }
}
