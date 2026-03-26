<?php
session_start();
require_once "../config/conexion.php";

if ($_POST) {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $equipo_id = $_POST['equipo_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $duracion = $_POST['duracion'];
    $lugar = $_POST['lugar'];
    $descripcion = $_POST['descripcion'];

    try {
        $sql = "UPDATE entrenamientos SET 
                titulo = ?, equipo_id = ?, fecha = ?, hora = ?, duracion = ?, lugar = ?, descripcion = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $equipo_id, $fecha, $hora, $duracion, $lugar, $descripcion, $id]);

        $_SESSION['paginaActual'] = 'entrenamientos';
        header("Location: menu.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}

$id = $_GET['id'] ?? null;

// Obtener datos del entrenamiento
$sql = "SELECT * FROM entrenamientos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$entrenamiento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entrenamiento) {
    die("Entrenamiento no encontrado");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Entrenamiento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #ecfdf5;
        padding: 20px;
    }

    .card-form {
        background: white;
        padding: 25px;
        border-radius: 12px;
        max-width: 600px;
        margin: 0 auto;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
    }

    .btn-verde {
        background: #22c55e;
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-verde:hover {
        background: #1ea34b;
    }

    .volver {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #16a34a;
        text-decoration: none;
    }

    .volver:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="card-form">
        <h2>Editar Entrenamiento</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= $entrenamiento['id'] ?>">

            <div class="form-group">
                <label>Tipo</label>
                <select name="titulo" required>
                    <option value="Sesión táctica"
                        <?= $entrenamiento['titulo'] == 'Sesión táctica' ? 'selected' : '' ?>>Sesión táctica</option>
                    <option value="Sesión técnica"
                        <?= $entrenamiento['titulo'] == 'Sesión técnica' ? 'selected' : '' ?>>Sesión técnica</option>
                    <option value="Sesión de físico"
                        <?= $entrenamiento['titulo'] == 'Sesión de físico' ? 'selected' : '' ?>>Sesión de física
                    </option>
                    <option value="Sesión pre-partido"
                        <?= $entrenamiento['titulo'] == 'Sesión pre-partido' ? 'selected' : '' ?>>Sesión pre-partido
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Equipo</label>
                <select name="equipo_id" required>
                    <?php
                    $equipos = $pdo->query("SELECT id, nombre FROM equipos");
                    while($e = $equipos->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <option value="<?= $e['id'] ?>" <?= $entrenamiento['equipo_id'] == $e['id'] ? 'selected' : '' ?>>
                        <?= $e['nombre'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="<?= $entrenamiento['fecha'] ?>" required>
            </div>

            <div class="form-group">
                <label>Hora</label>
                <input type="time" name="hora" value="<?= $entrenamiento['hora'] ?>" required>
            </div>

            <div class="form-group">
                <label>Duración (minutos)</label>
                <input type="number" name="duracion" value="<?= $entrenamiento['duracion'] ?>" required>
            </div>

            <div class="form-group">
                <label>Lugar</label>
                <input type="text" name="lugar" value="<?= $entrenamiento['lugar'] ?>">
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3"><?= htmlspecialchars($entrenamiento['descripcion']) ?></textarea>
            </div>

            <button type="submit" class="btn-verde">Actualizar Entrenamiento</button>
        </form>
        <a class="volver" href="menu.php">← Volver al menú</a>
    </div>
</body>

</html>