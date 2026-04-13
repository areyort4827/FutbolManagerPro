<?php
$club_id = $_SESSION['club_id'] ?? 1;

// Obtener todos los equipos del club
$sqlEquipos = "SELECT id, nombre, categoria FROM equipos WHERE equipo_id = ? ORDER BY nombre ASC";
$stmt = $pdo->prepare($sqlEquipos);
$stmt->execute([$club_id]);
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Equipo seleccionado
$equipoSeleccionado = isset($_POST['equipo']) ? (int)$_POST['equipo'] : 0;

// Consulta de jugadores
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
WHERE e.equipo_id = ?
";

if ($equipoSeleccionado > 0) {
    $sql .= " AND e.id = ?";
}

$sql .= " ORDER BY e.categoria ASC, j.posicion DESC";

$stmt = $pdo->prepare($sql);
$params = $equipoSeleccionado > 0 ? [$club_id, $equipoSeleccionado] : [$club_id];
$stmt->execute($params);
$jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Jugadores</title>

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 0;
            color: #374151;
        }

        .jugadoresContenedor {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .jugadoresHeader {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .header-left {
            flex: 1;
            min-width: 300px;
        }

        .header-right {
            flex-shrink: 0;
        }

        .jugadoresHeader h2 {
            margin: 0 0 6px 0;
            font-size: 26px;
            font-weight: 700;
        }

        .jugadoresHeader span {
            color: #64748b;
            font-size: 15px;
        }

        /* Filtro a la izquierda */
        .filtro-container {
            margin-top: 12px;
        }

        .filtro {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 15px;
            background: white;
            min-width: 260px;
        }

        /* Botón a la derecha */
        .btnAñadir {
            background: #16a34a;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
            transition: all 0.3s;
        }

        .btnAñadir:hover {
            background: #15803d;
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(22, 163, 74, 0.35);
        }

        #jugadoresGrid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 22px;
        }

        .jugadorCard {
            position: relative;
            background: #fff;
            border-radius: 14px;
            padding: 24px 20px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            transition: all 0.25s;
            text-align: center;
        }

        .jugadorCard:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
        }

        .deleteIcon {
            position: absolute;
            top: 12px;
            right: 12px;
            color: #9ca3af;
            font-size: 17px;
            padding: 6px;
            border-radius: 6px;
            transition: 0.2s;
        }

        .deleteIcon:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .avatar {
            font-size: 38px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .jugadorHeader h3 {
            margin: 6px 0 4px;
            font-size: 19px;
            font-weight: 600;
        }

        .posicion {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }

        .info {
            margin: 14px 0;
            font-size: 15px;
            line-height: 1.5;
        }

        .categoria {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .cadete   { background-color: #22c55e; }
        .senior   { background-color: #3b82f6; }
        .juvenil  { background-color: #f59e0b; }
        .infantil { background-color: #ef4444; }

        @media (max-width: 768px) {
            .jugadoresHeader {
                flex-direction: column;
                align-items: stretch;
            }
            .header-right {
                text-align: right;
            }
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
            <span>
                <?= ($equipoSeleccionado == 0) 
                    ? "Total de jugadores del club: " . count($jugadores) 
                    : "Jugadores en este equipo: " . count($jugadores) . "/25" 
                ?>
            </span>

            <!-- Filtro por equipo -->
            <div class="filtro-container">
                <form method="POST" action="">
                    <select name="equipo" id="equipo" class="filtro" onchange="this.form.submit()">
                        <option value="0">Todos los equipos</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?= $equipo['id'] ?>" <?= $equipoSeleccionado == $equipo['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($equipo['nombre'] . " (" . $equipo['categoria'] . ")") ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <!-- Derecha: Botón Añadir -->
        <div class="header-right">
            <a href="nuevo_jugador.php" class="btnAñadir">
                <i class="fas fa-plus"></i> 
                Añadir jugador
            </a>
        </div>

    </div>

    <!-- Grid de jugadores -->
    <div id="jugadoresGrid">
        <?php foreach ($jugadores as $jugador): ?>
        <div class="jugadorCard">
            <a href="eliminar_jugador.php?id=<?= $jugador['id'] ?>" 
               class="deleteIcon" 
               onclick="return confirm('¿Estás seguro de eliminar este jugador?')">
                <i class="fa-solid fa-trash"></i>
            </a>

            <div class="jugadorHeader">
                <i class="fa-regular fa-user avatar"></i>
                <h3><?= htmlspecialchars($jugador['jugador']) ?></h3>
                <span class="posicion"><?= strtoupper($jugador['posicion']) ?></span>
            </div>

            <p class="info">
                <?= $jugador['edad'] ?> años<br>
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