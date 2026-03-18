<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    .charts-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        /* 2 por fila */
        gap: 40px;
        max-width: 1100px;
        margin: 30px auto;
    }

    .chart-box {
        width: 100%;
        height: 350px;
        background: white;
        padding: 25px;
        border-radius: 12px;
        border: 2px solid #16a34a;
    }

    .victorias-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .victorias-chart {
        width: 55%;
    }

    .victorias-info {
        width: 45%;
        text-align: left;
        font-size: 1rem;
    }

    .victorias-info h3 {
        margin-top: 0;
        color: #16a34a;
    }

    .victorias-info p {
        margin: 8px 0;
    }

    /* indicadores de color */
    .verde,
    .amarillo,
    .rojo {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .verde {
        background: #16a34a;
    }

    .amarillo {
        background: #eab308;
    }

    .rojo {
        background: #ef4444;
    }

    .total {
        margin-top: 10px;
        font-size: 1.1rem;
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <h2>Estadísticas del Equipo</h2>

    <div class="charts-container">

        <div class="chart-box">
            <canvas id="rendimientoChart"></canvas>
        </div>

        <div class="chart-box">
            <canvas id="golesChart"></canvas>
        </div>
        <div class="chart-box victorias-box">

            <div class="victorias-chart">
                <canvas id="victoriasChart"></canvas>
            </div>

            <div class="victorias-info">
                <h3>Resumen</h3>

                <p><span class="verde"></span> Victorias: <strong id="totalVictorias"></strong></p>
                <p><span class="amarillo"></span> Empates: <strong id="totalEmpates"></strong></p>
                <p><span class="rojo"></span> Derrotas: <strong id="totalDerrotas"></strong></p>

                <hr>

                <p class="total">Partidos jugados:
                    <strong id="totalPartidos"></strong>
                </p>
            </div>


        </div>

        <script>
        /* ===== GRÁFICA 1: Rendimiento últimos partidos ===== */

        const rendimientoCtx = document.getElementById('rendimientoChart');

        new Chart(rendimientoCtx, {
            type: 'line',
            data: {
                labels: ['J1', 'J2', 'J3', 'J4', 'J5', 'J6'],
                datasets: [{
                    label: 'Puntuación del equipo',
                    data: [1, 6, 8, 7, 9, 5, 10],
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });


        /* ===== GRÁFICA 2: Goles por jugador ===== */

        const golesCtx = document.getElementById('golesChart');

        new Chart(golesCtx, {
            type: 'bar',
            data: {
                labels: ['Antonio', 'Emerson', 'Messi', 'Cristiano'],
                datasets: [{
                    label: 'Goles',
                    data: [12, 0, 8, 10],
                    backgroundColor: '#16a34a'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        /* ===== GRÁFICA 3: Victorias del equipo ===== */

        const victorias = 4;
        const empates = 1;
        const derrotas = 1;

        const total = victorias + empates + derrotas;

        /* ===== INSERTAR TEXTO ===== */
        document.getElementById("totalVictorias").textContent = victorias;
        document.getElementById("totalEmpates").textContent = empates;
        document.getElementById("totalDerrotas").textContent = derrotas;
        document.getElementById("totalPartidos").textContent = total;

        /* ===== GRÁFICA ===== */
        const victoriasCtx = document.getElementById('victoriasChart');

        new Chart(victoriasCtx, {
            type: 'doughnut',
            data: {
                labels: ['Victorias', 'Empates', 'Derrotas'],
                datasets: [{
                    data: [victorias, empates, derrotas],
                    backgroundColor: [
                        '#16a34a',
                        '#eab308',
                        '#ef4444'
                    ]
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        </script>
</body>

</html>