<?php
$usuario_sesion = $_SESSION['user'];
$identificador_usuario = $usuario_sesion['id'];

// Obtener el club al que pertenece el entrenador
$consulta_club = "SELECT equipo_id AS club_id FROM entrenadores WHERE usuario_id = $identificador_usuario";
$resultado_club = $pdo->query($consulta_club);
$datos_club = $resultado_club->fetch(PDO::FETCH_ASSOC);

if (!$datos_club) {
    die("Este entrenador no tiene club asignado");
}

$identificador_club = $datos_club['club_id'];

// Obtener los jugadores del club del entrenador
$consulta_jugadores = "
SELECT 
    jugadores.id,
    jugadores.nombre AS jugador,
    jugadores.edad,
    jugadores.posicion,
    equipos.nombre AS equipo,
    equipos.categoria
FROM jugadores 
INNER JOIN equipos ON jugadores.equipo_id = equipos.id
WHERE equipos.equipo_id = $identificador_club
ORDER BY equipos.categoria ASC, jugadores.posicion DESC
";

$resultado_jugadores = $pdo->query($consulta_jugadores);
$lista_jugadores = $resultado_jugadores->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .jugadoresContenedor {
        padding: 30px;
        font-family: 'Inter', sans-serif;
        color: #374151;
    }

    /* Header */
    .jugadoresHeader {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 25px;
    }

    .jugadoresHeader h2 {
        margin: 0;
        font-size: 24px;
    }

    .jugadoresHeader span {
        color: #64748b;
        font-size: 14px;
    }

    #jugadoresGrid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }

    .jugadorCard {
        position: relative;
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        transition: 0.25s;
        overflow: visible;
        text-align: center;
    }

    .jugadorCard:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
    }

    /* Avatar */
    .avatar {
        font-size: 30px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    /* Nombre y Posición */
    .jugadorHeader h3 {
        margin: 5px 0 2px;
        font-size: 18px;
        font-weight: 600;
    }

    .posicion {
        font-size: 12px;
        color: #6b7280;
        letter-spacing: .5px;
    }

    /* Info jugador */
    .info {
        margin: 12px 0;
        font-size: 14px;
        color: #374151;
    }

    /* Categoría badge */
    .categoria {
        display: inline-block;
        margin-top: 10px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        color: white;
    }

    /* Colores por categoría */
    .cadete {
        background-color: #22c55e;
    }

    .senior {
        background-color: #3b82f6;
    }

    .juvenil {
        background-color: #f59e0b;
    }

    .infantil {
        background-color: #ef4444;
    }
</style>

<div class="jugadoresContenedor">
    <div class="jugadoresHeader">
        <div>
            <h2>Plantilla del Club</h2>
            <span>Revisa los jugadores disponibles en tu club</span><br>
            <span>Total de jugadores: <?= count($lista_jugadores) ?></span>
        </div>
    </div>

    <div id="jugadoresGrid">
        <?php foreach ($lista_jugadores as $jugador): ?>
        <div class="jugadorCard">

            <div class="jugadorHeader">
                <i class="fa-regular fa-user avatar"></i>
                <h3><?= htmlspecialchars($jugador['jugador']) ?></h3>
                <span class="posicion"><?= strtoupper(htmlspecialchars($jugador['posicion'])) ?></span>
            </div>

            <p class="info">
                <?= htmlspecialchars($jugador['edad']) ?> años<br>
                <?= htmlspecialchars($jugador['equipo']) ?>
            </p>

            <span class="categoria <?= strtolower(htmlspecialchars($jugador['categoria'])) ?>">
                <?= htmlspecialchars($jugador['categoria']) ?>
            </span>

        </div>
        <?php endforeach; ?>
    </div>
</div>