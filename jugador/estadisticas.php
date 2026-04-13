<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Análisis del rendimiento del club</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }

        /* Tarjetas superiores - FORZADO a 4 en una fila */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);     /* 4 columnas siempre */
            gap: 24px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 28px 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            min-height: 160px;
        }

        .card:hover {
            transform: translateY(-6px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            height: 100%;
        }

        .card-title {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 2.7rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }

        .card-sub {
            font-size: 0.95rem;
            margin-top: 8px;
        }

        .icon {
            font-size: 3.2rem;
            color: #10b981;
            flex-shrink: 0;
        }

        /* Gráficos */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            font-size: 1.35rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: #1f2937;
        }

        /* Máximos Goleadores */
        .top-scorers {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .player {
            display: flex;
            align-items: center;
            background: #f8fafc;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 16px;
        }

        .player:last-child {
            margin-bottom: 0;
        }

        .rank {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        .rank-1 { background: #fbbf24; }
        .rank-2 { background: #9ca3af; }

        .player-info {
            flex: 1;
            margin-left: 20px;
        }

        .player-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .goals {
            font-size: 2.2rem;
            font-weight: 700;
            color: #10b981;
            text-align: right;
        }

        /* Responsive: en pantallas más pequeñas */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);   /* 2 columnas en tablets */
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;              /* 1 columna en móviles */
            }
            .charts-grid {
                grid-template-columns: 1fr;
            }
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Estadísticas</h1>
    <p class="subtitle">Análisis del rendimiento del club</p>

    <!-- Tarjetas superiores (ahora siempre 4 en fila en desktop) -->
    <div class="stats-grid">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Partidos Jugados</div>
                    <div class="card-value">43</div>
                    <div class="card-sub" style="color:#10b981;">23V - 10E - 8D</div>
                </div>
                <div class="icon"><i class="fas fa-futbol"></i></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">% Victorias</div>
                    <div class="card-value">58%</div>
                    <div class="card-sub" style="color:#10b981;">Tasa de éxito</div>
                </div>
                <div class="icon"><i class="fas fa-trophy"></i></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Diferencia Goles</div>
                    <div class="card-value" style="color:#10b981;">+35</div>
                    <div class="card-sub">79 a favor / 44 en contra</div>
                </div>
                <div class="icon"><i class="fas fa-exchange-alt"></i></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Asistencia Media</div>
                    <div class="card-value">N/A</div>
                    <div class="card-sub">A entrenamientos</div>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">Rendimiento Mensual</div>
            <canvas id="rendimientoChart" height="110"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-title">Evolución de Goles</div>
            <canvas id="golesChart" height="110"></canvas>
        </div>
    </div>

    <!-- Máximos Goleadores -->
    <div class="top-scorers">
        <div class="chart-title">Máximos Goleadores</div>
        
        <div class="player">
            <div class="rank rank-1">1</div>
            <div class="player-info">
                <div class="player-name">Carlos Ruiz</div>
                <div style="color:#6b7280; font-size:0.95rem;">9 asistencias</div>
            </div>
            <div>
                <div class="goals">12</div>
                <div style="text-align:right; font-size:0.85rem; color:#6b7280;">goles</div>
            </div>
        </div>

        <div class="player">
            <div class="rank rank-2">2</div>
            <div class="player-info">
                <div class="player-name">David Morales</div>
            </div>
            <div>
                <div class="goals">9</div>
                <div style="text-align:right; font-size:0.85rem; color:#6b7280;">goles</div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico Rendimiento Mensual
const ctx1 = document.getElementById('rendimientoChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ['Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            { label: 'Victorias', data: [4,5,3,6,4,3], backgroundColor: '#10b981' },
            { label: 'Empates',   data: [2,2,3,2,2,1], backgroundColor: '#eab308' },
            { label: 'Derrotas',  data: [1,0,1,0,1,1], backgroundColor: '#ef4444' }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, max: 7 } }
    }
});

// Gráfico Evolución de Goles
const ctx2 = document.getElementById('golesChart').getContext('2d');
new Chart(ctx2, {
    type: 'line',
    data: {
        labels: ['Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [
            {
                label: 'Goles a favor',
                data: [13,15,12,19,14,10],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                borderWidth: 3
            },
            {
                label: 'Goles en contra',
                data: [8,10,9,6,9,2],
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                borderWidth: 3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>