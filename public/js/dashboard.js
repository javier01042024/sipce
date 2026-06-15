document.addEventListener('DOMContentLoaded', function() {
    
    // ====================================================
    // FECHA ACTUAL
    // ====================================================
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateElement = document.getElementById('currentDate');
    if (dateElement) {
        dateElement.textContent = new Date().toLocaleDateString('es-ES', options);
    }
    
    // ====================================================
    // GRÁFICAS
    // ====================================================
    let pacientesChart, distribucionChart;
    
    // Gráfica de evolución de pacientes
    const ctx1 = document.getElementById('pacientesChart')?.getContext('2d');
    if (ctx1) {
        pacientesChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: window.pacientesLabels || ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                datasets: [{
                    label: 'Nuevos pacientes',
                    data: window.pacientesData || [0, 0, 0, 0],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1e293b', titleColor: '#fff', bodyColor: '#94a3b8' }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#e2e8f0' },
                        ticks: { stepSize: 1 }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    
    // Gráfica de distribución
    const ctx2 = document.getElementById('distribucionChart')?.getContext('2d');
    if (ctx2) {
        distribucionChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: window.distribucionLabels || ['Alta', 'Media', 'Baja'],
                datasets: [{
                    data: window.distribucionData || [0, 0, 0],
                    backgroundColor: window.distribucionColors || ['#ef4444', '#f59e0b', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, font: { size: 13, family: "'Inter', sans-serif" } }
                    },
                    tooltip: { backgroundColor: '#1e293b' }
                },
                cutout: '65%'
            }
        });
    }
    
    // ====================================================
    // SELECTORES DE GRÁFICAS
    // ====================================================
    
    // Selector de período (evolución)
    const periodoSelect = document.getElementById('periodoSelect');
    if (periodoSelect) {
        periodoSelect.addEventListener('change', function() {
            const periodo = this.value;
            fetch(`/dashboard/chart-data?periodo=${periodo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && pacientesChart) {
                        pacientesChart.data.labels = data.labels;
                        pacientesChart.data.datasets[0].data = data.data;
                        pacientesChart.update();
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }
    
    // Selector de tipo de distribución
    const tipoChartSelect = document.getElementById('tipoChartSelect');
    if (tipoChartSelect) {
        tipoChartSelect.addEventListener('change', function() {
            const tipo = this.value;
            fetch(`/dashboard/distribucion-data?tipo=${tipo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && distribucionChart) {
                        distribucionChart.data.labels = data.labels;
                        distribucionChart.data.datasets[0].data = data.data;
                        distribucionChart.data.datasets[0].backgroundColor = data.colors;
                        distribucionChart.update();
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }
    
    // ====================================================
    // ACTUALIZACIÓN AUTOMÁTICA (cada 30 segundos)
    // ====================================================
    setInterval(function() {
        fetch('/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar números en las tarjetas
                    document.querySelectorAll('.stat-number').forEach(el => {
                        const key = el.closest('.stat-card')?.getAttribute('data-key');
                        if (key && data[key]) {
                            el.textContent = data[key];
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }, 30000);
});