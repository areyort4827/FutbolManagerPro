<style>
.tabla {
    width: 100%;
    border-collapse: collapse;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.tabla th, .tabla td {
    padding: 15px;
    text-align: left;
}
.tabla th {
    background: #16a34a;
    color: white;
}
.tabla tr:nth-child(even){ background: #d1fae5; }
.tabla tr:hover { background: #a7f3d0; cursor: pointer; }
.resultado.victoria { color: #16a34a; font-weight: bold; }
.resultado.empate { color: #eab308; font-weight: bold; }
.resultado.derrota { color: #ef4444; font-weight: bold; }
</style>

<h2>Partidos</h2>
<table class="tabla">
    <thead>
        <tr>
            <th>Fecha</th><th>Rival</th><th>Lugar</th><th>Resultado</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>20/03/2026</td><td>Equipo A</td><td>Local</td><td class="resultado victoria">3-1</td></tr>
        <tr><td>25/03/2026</td><td>Equipo B</td><td>Visitante</td><td class="resultado empate">2-2</td></tr>
        <tr><td>30/03/2026</td><td>Equipo C</td><td>Local</td><td class="resultado derrota">0-1</td></tr>
    </tbody>
</table>