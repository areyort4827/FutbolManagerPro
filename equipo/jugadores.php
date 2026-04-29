<?php
$club_id = $_SESSION['club_id'];

// Obtener todos los equipos del club
$sqlEquipos = "SELECT id, nombre, categoria FROM equipos WHERE equipo_id = $club_id ORDER BY nombre ASC";
$resultadoEquipos = $pdo->query($sqlEquipos);
$equipos = $resultadoEquipos->fetchAll(PDO::FETCH_ASSOC);

// Equipo seleccionado para esta carga puntual del listado
$equipoSeleccionado = isset($_SESSION['filtroEquipoJugadores']) ? (int)$_SESSION['filtroEquipoJugadores'] : 0;
unset($_SESSION['filtroEquipoJugadores']);

// Consulta de jugadores filtrada (incluye eliminados con LEFT JOIN)
$sql = "
SELECT 
    j.id,
    j.nombre AS jugador,
    TIMESTAMPDIFF(YEAR, j.fecha_nacimiento, CURDATE()) AS edad,
    j.posicion,
    COALESCE(e.nombre, 'Sin equipo') AS equipo,
    COALESCE(e.categoria, '') AS categoria,
    COALESCE(c.nombre, '—') AS club,
    j.eliminado
FROM jugadores j
LEFT JOIN equipos e ON j.equipo_id = e.id
LEFT JOIN clubes c ON e.equipo_id = c.id
";

if ($equipoSeleccionado == -1) {
    // Antiguos jugadores: eliminados que pertenecían a este club
    $sql .= " WHERE j.eliminado = 1 AND j.equipo_anterior_id IN (SELECT id FROM equipos WHERE equipo_id = $club_id)";
} elseif ($equipoSeleccionado > 0) {
    // Equipo concreto del club
    $sql .= " WHERE e.id = $equipoSeleccionado AND e.equipo_id = $club_id";
} else {
    // Todos los jugadores activos del club
    $sql .= " WHERE j.eliminado = 0 AND e.equipo_id = $club_id";
}

$sql .= " ORDER BY j.eliminado ASC, e.categoria ASC, j.posicion DESC";

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
    .filial { background-color: #176df7; }

    /* Botón Refichar */
    .reficharIcon {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #2563eb;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .reficharIcon:hover { background: #dbeafe; }

    /* Modal Refichar */
    .modal-refichar {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-refichar.activo { display: flex; }
    .modal-refichar-content {
        background: white;
        border-radius: 14px;
        padding: 32px;
        width: 360px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .modal-refichar-content h3 { margin: 0 0 6px; color: #1f2937; }
    .modal-refichar-content p  { color: #6b7280; font-size: 14px; margin: 0 0 20px; }
    .modal-refichar-content select {
        width: 100%; padding: 10px; border-radius: 8px;
        border: 1px solid #d1d5db; font-size: 15px; margin-bottom: 20px;
    }
    .modal-refichar-btns { display: flex; gap: 10px; }
    .btn-confirmar-refichar {
        flex: 1; padding: 11px; background: #2563eb; color: white;
        border: none; border-radius: 8px; font-weight: 600; cursor: pointer;
        transition: 0.2s;
    }
    .btn-confirmar-refichar:hover { background: #1d4ed8; }
    .btn-cancelar-refichar {
        flex: 1; padding: 11px; background: #f3f4f6; color: #374151;
        border: none; border-radius: 8px; font-weight: 600; cursor: pointer;
    }
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
                            <option value="-1" <?= $equipoSeleccionado == -1 ? 'selected' : '' ?>>Antiguos jugadores</option>
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

                <!-- Iconos de acción: Editar / Eliminar / Refichar -->
                <div class="action-icons">
                    <?php if ($jugador['eliminado']): ?>
                        <button class="reficharIcon" title="Refichar jugador"
                            onclick="abrirRefichar(<?= $jugador['id'] ?>, '<?= htmlspecialchars(addslashes($jugador['jugador'])) ?>')">
                            <i class="fa-solid fa-rotate-left"></i> Refichar
                        </button>
                    <?php else: ?>
                        <!-- Editar -->
                        <a href="editar_jugador.php?id=<?= $jugador['id'] ?>" class="editIcon" title="Editar jugador">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <!-- Eliminar -->
                        <a href="eliminar_jugador.php?id=<?= $jugador['id'] ?>" class="deleteIcon"
                            onclick="return confirm('¿Eliminar este jugador? Esta acción no se puede deshacer.')">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Header -->
                <div class="jugadorHeader">
                    <i class="fa-regular fa-user avatar"></i>
                    <h3><?= htmlspecialchars($jugador['jugador']) ?></h3>
                    <span class="posicion"><?= strtoupper(htmlspecialchars($jugador['posicion'])) ?></span>
                </div>

                <!-- Info -->
                <p class="info">
                    <?= $jugador['edad'] !== null ? $jugador['edad'] . ' años' : '— años' ?><br>
                    <?= htmlspecialchars($jugador['equipo']) ?>
                </p>

                <?php if ($jugador['eliminado']): ?>
                    <span class="categoria" style="background:#6b7280;">Sin equipo</span>
                <?php elseif ($jugador['categoria']): ?>
                    <span class="categoria <?= strtolower($jugador['categoria']) ?>">
                        <?= htmlspecialchars($jugador['categoria']) ?>
                    </span>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        </div>
    </div>

<!-- ===== MODAL REFICHAR ===== -->
<div id="modalRefichar" class="modal-refichar">
    <div class="modal-refichar-content">
        <h3>Refichar jugador</h3>
        <p id="reficharNombre" style="color:#6b7280; font-size:14px; margin:0 0 20px;"></p>
        <form method="POST" action="refichar_jugador.php">
            <input type="hidden" name="jugador_id" id="refichar_jugador_id">
            <label style="font-size:14px; color:#374151; display:block; margin-bottom:6px;">Asignar a equipo:</label>
            <select name="equipo_id" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;font-size:15px;margin-bottom:20px;">
                <option value="">Selecciona un equipo</option>
                <?php foreach ($equipos as $eq): ?>
                <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?> (<?= htmlspecialchars($eq['categoria']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <div style="display:flex;gap:10px;">
                <button type="submit" style="flex:1;padding:11px;background:#2563eb;color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
                    <i class="fa-solid fa-rotate-left"></i> Confirmar fichaje
                </button>
                <button type="button" onclick="cerrarRefichar()" style="flex:1;padding:11px;background:#f3f4f6;color:#374151;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirRefichar(id, nombre) {
    document.getElementById('refichar_jugador_id').value = id;
    document.getElementById('reficharNombre').textContent = '¿Refichar a ' + nombre + '?';
    document.getElementById('modalRefichar').classList.add('activo');
}
function cerrarRefichar() {
    document.getElementById('modalRefichar').classList.remove('activo');
}
document.getElementById('modalRefichar').addEventListener('click', function(e) {
    if (e.target === this) cerrarRefichar();
});
</script>
</body>

</html>