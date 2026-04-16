<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$club_id = $_SESSION['club_id'] ?? 0;

if ($club_id == 0) {
        header("Location: ../index.php");
    exit;
}

require_once '../config/conexion.php';  

// PROCESAR ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)$_POST['id'];
    $nombre    = trim($_POST['nombre']);
    $edad      = (int)$_POST['edad'];
    $posicion  = $_POST['posicion'];
    $equipo_id = (int)$_POST['equipo_id'];

    if ($id > 0 && !empty($nombre) && $edad > 0 && !empty($posicion) && $equipo_id > 0) {
        $sql = "UPDATE jugadores 
                SET nombre = :nombre, 
                    edad = :edad, 
                    posicion = :posicion, 
                    equipo_id = :equipo_id 
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'    => $nombre,
            ':edad'      => $edad,
            ':posicion'  => $posicion,
            ':equipo_id' => $equipo_id,
            ':id'        => $id
        ]);

        $_SESSION['paginaActual'] = 'jugadores';
        header("Location: menu.php");
        exit;
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}

// CARGAR DATOS DEL JUGADOR
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $sql = "
    SELECT j.id, j.nombre, j.edad, j.posicion, j.equipo_id,
           e.nombre AS equipo_nombre, e.categoria
    FROM jugadores j
    INNER JOIN equipos e ON j.equipo_id = e.id
    WHERE j.id = :id AND e.equipo_id = :club_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id, ':club_id' => $club_id]);
    $jugador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$jugador) {
        echo "Jugador no encontrado o no tienes permiso para editarlo.";
        exit;
    }
} else {
    echo "ID de jugador no proporcionado.";
    exit;
}

// Obtener equipos del club
$sqlEquipos = "SELECT id, nombre, categoria FROM equipos WHERE equipo_id = :club_id ORDER BY nombre ASC";
$stmtEquipos = $pdo->prepare($sqlEquipos);
$stmtEquipos->execute([':club_id' => $club_id]);
$equipos = $stmtEquipos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Jugador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    body {
        font-family: 'Inter', sans-serif;
        background: #ecfdf5;
        margin: 0;
        padding: 0;
        color: #374151;
    }

    .contenedor {
        max-width: 620px;
        margin: 40px auto;
        padding: 30px;
    }

    .card {
        background: #fff;
        border-radius: 14px;
        padding: 35px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    }

    h2 {
        margin: 0 0 30px 0;
        font-size: 24px;
        color: #1e2937;
    }

    label {
        display: block;
        margin: 18px 0 6px;
        font-weight: 600;
        color: #374151;
    }

    input, select {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 16px;
        box-sizing: border-box;
    }

    input:focus, select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    /* Botones - Estilo unificado con tu app */
    .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: 0.25s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-guardar {
        background: #16a34a;
        color: white;
    }

    .btn-guardar:hover {
        background: #15803d;
        transform: translateY(-2px);
    }

    .btn-volver {
        background: #64748b;
        color: white;
    }

    .btn-volver:hover {
        background: #475569;
    }

    .error {
        background: #fee2e2;
        color: #dc2626;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="card">
        <h2><i class="fa-solid fa-user-pen"></i> Editar Jugador</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= $jugador['id'] ?>">

            <label>Nombre del jugador</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($jugador['nombre']) ?>" required>

            <label>Edad</label>
            <input type="number" name="edad" value="<?= $jugador['edad'] ?>" min="5" max="60" required>

            <label>Posición</label>
            <select name="posicion" required>
                <option value="Portero"     <?= $jugador['posicion'] === 'Portero'     ? 'selected' : '' ?>>Portero</option>
                <option value="Defensa"     <?= $jugador['posicion'] === 'Defensa'     ? 'selected' : '' ?>>Defensa</option>
                <option value="Mediocentro" <?= $jugador['posicion'] === 'Mediocentro' ? 'selected' : '' ?>>Mediocentro</option>
                <option value="Delantero"   <?= $jugador['posicion'] === 'Delantero'   ? 'selected' : '' ?>>Delantero</option>
            </select>

            <label>Equipo</label>
            <select name="equipo_id" required>
                <?php foreach ($equipos as $eq): ?>
                    <option value="<?= $eq['id'] ?>" 
                        <?= $eq['id'] == $jugador['equipo_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($eq['nombre']) ?> (<?= htmlspecialchars($eq['categoria']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <div style="margin-top: 35px; display: flex; gap: 12px; flex-wrap: wrap;">
                <button type="submit" class="btn btn-guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
                
                <a href="menu.php" class="btn btn-volver">
                    <i class="fa-solid fa-arrow-left"></i> Volver a la lista
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>