// ===== DASHBOARD =====

// Fecha actual
const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
document.getElementById('currentDate').textContent = new Date().toLocaleDateString('es-ES', options);

// Gráficas
let pacientesChart, prioridadChart;

document.addEventListener('DOMContentLoaded', function() {
    const ctx1 = document.getElementById('pacientesChart').getContext('2d');
    pacientesChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: window.evolucionPacientes.labels,
            datasets: [{
                label: 'Nuevos pacientes',
                data: window.evolucionPacientes.data,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#e2e8f0' },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            if (Math.floor(value) === value) return value;
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
    
    const ctx2 = document.getElementById('prioridadChart').getContext('2d');
    prioridadChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Alta', 'Media', 'Baja'],
            datasets: [{
                data: [
                    window.distribucionPrioridad.alta,
                    window.distribucionPrioridad.media,
                    window.distribucionPrioridad.baja
                ],
                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 12, font: { size: 12 } }
                }
            },
            cutout: '65%'
        }
    });
});

// Selector de período
document.getElementById('periodoSelect').addEventListener('change', function() {
    const periodo = this.value;
    let labels, data;
    
    if (periodo === 'semana') {
        labels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        data = [3, 5, 2, 7, 4, 1, 3];
    } else if (periodo === 'mes') {
        labels = window.evolucionPacientes.labels;
        data = window.evolucionPacientes.data;
    } else {
        labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        data = [8, 12, 10, 15, 13, 18, 20, 16, 19, 22, 25, 28];
    }
    
    pacientesChart.data.labels = labels;
    pacientesChart.data.datasets[0].data = data;
    pacientesChart.update();
});

// Selector de tipo de gráfica
document.getElementById('tipoChartSelect').addEventListener('change', function() {
    const tipo = this.value;
    
    if (tipo === 'prioridad') {
        prioridadChart.data.labels = ['Alta', 'Media', 'Baja'];
        prioridadChart.data.datasets[0].data = [
            window.distribucionPrioridad.alta,
            window.distribucionPrioridad.media,
            window.distribucionPrioridad.baja
        ];
        prioridadChart.data.datasets[0].backgroundColor = ['#ef4444', '#f59e0b', '#10b981'];
    } else {
        prioridadChart.data.labels = ['18-30', '31-50', '51-70', '70+'];
        prioridadChart.data.datasets[0].data = [
            window.distribucionEdad['18-30'] || 0,
            window.distribucionEdad['31-50'] || 0,
            window.distribucionEdad['51-70'] || 0,
            window.distribucionEdad['70+'] || 0
        ];
        prioridadChart.data.datasets[0].backgroundColor = ['#667eea', '#764ba2', '#f39c12', '#ef4444'];
    }
    
    prioridadChart.update();
});

// Redimensionar gráficas al cambiar orientación
window.addEventListener('resize', function() {
    if (pacientesChart) pacientesChart.resize();
    if (prioridadChart) prioridadChart.resize();
});
