@extends('layouts.dashboard')

@section('page_title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<section class="content container-fluid">
    <div class="card card-primary card-outline mb-4">
        <div class="card-body">
            <p>Bienvenido al panel de administración "Buscar dentro de un libro".</p>
            <ul>
                <li>Utilice el menú lateral para acceder a la búsqueda de libros.</li>
                <li>Este panel de control se puede ampliar con nuevas funciones.</li>
            </ul>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12 mb-4 dashboard-chart-container">
            <canvas id="chart-bar"></canvas>
        </div>
        <div class="col-lg-8 col-md-10 col-12 mb-4 dashboard-chart-container">
            <canvas id="chart-line"></canvas>
        </div>
        <div class="col-md-6 col-12 mb-4 dashboard-chart-container">
            <canvas id="chart-pie"></canvas>
        </div>
        <div class="col-md-6 col-12 mb-4 dashboard-chart-container">
            <canvas id="chart-doughnut"></canvas>
        </div>
        <div class="col-lg-8 col-md-10 col-12 mb-4 dashboard-chart-container">
            <canvas id="chart-radar"></canvas>
        </div>
    </div>
</section>
@endsection

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Bar Chart
        const ctxBar = document.getElementById('chart-bar').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['JavaScript', 'React', 'Laravel', 'API', 'Node', 'PHP', 'Frontend'],
                datasets: [{
                    label: 'Termos mais buscados',
                    data: [18, 14, 10, 8, 7, 6, 4],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(144, 238, 144, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Termos mais buscados (Barra)'
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Line Chart
        const ctxLine = document.getElementById('chart-line').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['0h', '3h', '6h', '9h', '12h', '15h', '18h', '21h', '23h'],
                datasets: [{
                    label: 'Buscas por horário',
                    data: [2, 3, 5, 8, 13, 11, 7, 4, 2],
                    fill: false,
                    borderColor: 'rgba(54, 162, 235, 0.7)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Buscas por horário (Linha)'
                    }
                }
            }
        });

        // Pie Chart
        const ctxPie = document.getElementById('chart-pie').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Categoria A', 'Categoria B', 'Categoria C', 'Categoria D'],
                datasets: [{
                    label: 'Percentual por categoria',
                    data: [40, 25, 20, 15],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Percentual por categoria (Pizza)'
                    }
                }
            }
        });

        // Doughnut Chart
        const ctxDoughnut = document.getElementById('chart-doughnut').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Sucesso', 'Falha'],
                datasets: [{
                    label: 'Taxa de sucesso das buscas',
                    data: [85, 15],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Taxa de sucesso das buscas (Doughnut)'
                    }
                }
            }
        });

        // Radar Chart
        const ctxRadar = document.getElementById('chart-radar').getContext('2d');
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Distribuição por dia da semana',
                    data: [12, 19, 8, 15, 10, 7, 5],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribuição por dia da semana (Radar)'
                    }
                }
            }
        });
    });
</script>

<style>
    .dashboard-chart-container {
        max-width: 700px;
        margin: 0 auto 2rem auto;
    }
    canvas {
        max-width: 100% !important;
        height: 320px !important;
    }
</style>
