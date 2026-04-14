<?php
$club_id = $_SESSION['club_id'];

// Obtener todos los equipos del club
$sqlEquipos = "SELECT id, nombre, categoria FROM equipos WHERE equipo_id = $club_id ORDER BY nombre ASC";
$resultadoEquipos = $pdo->query($sqlEquipos);
$equipos = $resultadoEquipos->fetchAll(PDO::FETCH_ASSOC);

// Equipo seleccionado (por POST)
$equipoSeleccionado = isset($_POST['equipo']) ? (int)$_POST['equipo'] : 0;

// Consulta de jugadores filtrada
$sql = "
SELECT 
    j.id,
    j.nombre AS jugador,
    j.edad,
    j.posicion,
    e.nombre AS equipo,
    e.categoria,
    c.nombre AS club
FROM jugadores j
INNER JOIN equipos e ON j.equipo_id = e.id
INNER JOIN clubes c ON e.equipo_id = c.id
WHERE e.equipo_id = $club_id
";

// Filtrar por equipo si se selecciona uno
if ($equipoSeleccionado > 0) {
    $sql .= " AND e.id = $equipoSeleccionado";
}

$sql .= " ORDER BY e.categoria ASC, j.posicion DESC";

$resultado = $pdo->query($sql);
$jugadores = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Jugadores</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    body {
        font-family: 'Inter', sans-serif;
        background: #f5f7fb;
        margin: 0;
        padding: 0;
        color: #374151;
    }

    .jugadoresContenedor {
        padding: 30px;
    }

    /* Header */
    .jugadoresHeader {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 25px;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header-left {
        flex: 1;
    }

    .jugadoresHeader h2 {
        margin: 0;
        font-size: 24px;
    }

    .jugadoresHeader span {
        color: #64748b;
        font-size: 14px;
    }

    .filtro-container {
        margin-top: 10px;
    }

    /* Botón Añadir */
    .btnAñadir {
        background: #16a34a;
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        transition: 0.25s;
        white-space: nowrap;
        height: fit-content;
        margin-top: 10px;
    }

    .btnAñadir:hover {
        background: #15803d;
        transform: translateY(-2px);
    }

    /* Select filtro */
    form select {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
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

    /* Contenedor de iconos de acción */
    .action-icons {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 6px;
        z-index: 10;
    }

    /* Icono Editar */
    .editIcon {
        color: #64748b;
        font-size: 16px;
        text-decoration: none;
        padding: 6px;
        border-radius: 8px;
        transition: 0.2s;
    }

    .editIcon:hover {
        background: #dbeafe;
        color: #2563eb;
    }

    /* Icono Eliminar */
    .deleteIcon {
        color: #9ca3af;
        font-size: 16px;
        text-decoration: none;
        padding: 6px;
        border-radius: 8px;
        transition: 0.2s;
    }

    .deleteIcon:hover {
        background: #fee2e2;
        color: #dc2626;
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

    .cadete   { background-color: #22c55e; }
    .senior   { background-color: #3b82f6; }
    .juvenil  { background-color: #f59e0b; }
    .infantil { background-color: #ef4444; }
    </style>
</head>

<body>
    <div class="jugadoresContenedor">
        <div class="jugadoresHeader">
            
            <!-- Izquierda: Título + Filtro -->
            <div class="header-left">
                <h2>Gestión de Jugadores</h2>
                <span>Gestiona tu plantilla</span><br>
                <span><?= ($equipoSeleccionado == 0) ? "Total de jugadores del club: ". count($jugadores) : "Total de jugadores del equipo: ".count($jugadores) . "/25" ?></span>
                
                <!-- Select filtro -->
                <div class="filtro-container">
                    <form method="POST" action="">
                        <label for="equipo">Filtrar por equipo:</label>
                        <select name="equipo" id="equipo" onchange="this.form.submit()">
                            <option value="0">Todos los equipos</option>
                            <?php foreach ($equipos as $equipo): ?>
                            <option value="<?= $equipo['id']?>" <?= $equipoSeleccionado == $equipo['id'] ? 'selected' : '' ?>>
                                <?= $equipo['nombre'] . " (" . $equipo['categoria'] . ")"  ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Derecha: Botón Añadir -->
            <a href="nuevo_jugador.php" class="btnAñadir">+ Añadir jugador</a>

        </div>

        <div id="jugadoresGrid">
            <?php foreach ($jugadores as $jugador): ?>
            <div class="jugadorCard">

                <!-- Iconos de acción: Editar y Eliminar -->
                <div class="action-icons">
                    <!-- Editar -->
                    <a href="editar_jugador.php?id=<?= $jugador['id'] ?>" class="editIcon" title="Editar jugador">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    
                    <!-- Eliminar -->
                    <a href="eliminar_jugador.php?id=<?= $jugador['id'] ?>" class="deleteIcon"
                        onclick="return confirm('¿Eliminar este jugador? Esta acción no se puede deshacer.')">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </div>

                <!-- Header -->
                <div class="jugadorHeader">
                    <i class="fa-regular fa-user avatar"></i>
                    <h3><?= htmlspecialchars($jugador['jugador']) ?></h3>
                    <span class="posicion"><?= strtoupper(htmlspecialchars($jugador['posicion'])) ?></span>
                </div>

                <!-- Info -->
                <p class="info">
                    <?= htmlspecialchars($jugador['edad']) ?> años<br>
                    <?= htmlspecialchars($jugador['equipo']) ?>
                </p>

                <span class="categoria <?= strtolower($jugador['categoria']) ?>">
                    <?= htmlspecialchars($jugador['categoria']) ?>
                </span>

            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>