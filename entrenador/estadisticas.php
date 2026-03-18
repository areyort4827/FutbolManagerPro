<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    .charts-container {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        justify-content: center;
        margin-top: 30px;
    }

    .chart-box {
        width: 500px;
        max-width: 90%;
        background: white;
        padding: 25px;
        border-radius: 12px;
        border: 2px solid #16a34a;
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

    </div>

     <script>
/* ===== GRÁFICA 1: Rendimiento últimos partidos ===== */

const rendimientoCtx = document.getElementById('rendimientoChart');

new Chart(rendimientoCtx, {
    type: 'line',
    data: {
        labels: ['J1','J2','J3','J4','J5','J6'],
        datasets: [{
            label: 'Puntuación del equipo',
            data: [6, 8, 7, 9, 5, 10],
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
                    font: { size: 14 }
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
        labels: ['Antonio','Emerson','Messi','Cristiano'],
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
</script>
</body>

</html>