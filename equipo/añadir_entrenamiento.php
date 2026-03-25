<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Entrenamiento</title>
    <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: #ecfdf5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .contenido h2 {
        color: #16a34a;
        text-align: center;
        margin-bottom: 25px;
    }

    .card-form {
        background: white;
        padding: 25px;
        border-radius: 12px;
        width: 600px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .btn-verde {
        background: #22c55e;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
    }

    .volver {
        display: block;
        text-align: center;
        margin-top: 20px;
        text-decoration: none;
        color: #16a34a;
    }

    .volver:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <?php
session_start();
require_once "../config/conexion.php";

// GUARDAR ENTRENAMIENTO
if (isset($_POST['guardar'])) {

    $equipo_id = $_POST['equipo_id'];
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $duracion = $_POST['duracion'];
    $lugar = $_POST['lugar'];
    $descripcion = $_POST['descripcion'];

    try {

        $sql = "INSERT INTO entrenamientos 
                (equipo_id,titulo, fecha, hora,duracion, lugar, descripcion)
                VALUES (:equipo_id,:titulo, :fecha, :hora, :duracion, :lugar, :descripcion)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':equipo_id' => $equipo_id,
            ':titulo' => $titulo,
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':duracion' => $duracion,
            ':lugar' => $lugar,
            ':descripcion' => $descripcion
        ]);
    $_SESSION['paginaActual'] = 'entrenamientos';
        header("Location: menu.php");
        exit;

    } catch (PDOException $e) {
        echo "Error al guardar entrenamiento: " . $e->getMessage();
    }
}
?>

    <div class="contenido">

        <h2>Añadir Entrenamiento</h2>

        <div class="card-form">

            <form method="POST">

                <div class="form-group">
                    <label>Tipo</label>
                    <select name="titulo" required>
            <option value="">Seleccionar tipo de entrenamiento</option>
            <option value="Sesión táctica">Sesión táctica</option>
            <option value="Sesión técnica">Sesión técnica</option>
            <option value="Sesión de físico">Sesión de físico</option>
            <option value="Sesión pre-partido">Sesión pre-partido</option>
        </select>
                    </select>
                </div>

                    <div class="form-group">
                        <label>Equipo</label>
                        <select name="equipo_id" required>
                            <?php    
            $equipos = $pdo->query("SELECT id, nombre FROM equipos");
            while($e = $equipos->fetch(PDO::FETCH_ASSOC)){
                echo "<option value='{$e['id']}'>{$e['nombre']}</option>";
            }
         ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="fecha" required>
                    </div>

                    <div class="form-group">
                        <label>Hora</label>
                        <input type="time" name="hora" required>
                    </div>

                    <div class="form-group">
                        <label>Duración (minutos)</label>
                        <input type="number" name="duracion" required>
                    </div>

                    <div class="form-group">
                        <label>Lugar</label>
                        <input type="text" name="lugar" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion"></textarea>
                    </div>

                    <button class="btn-verde" name="guardar">
                        Guardar Entrenamiento
                    </button>

            </form>
            <a class="volver" href="menu.php">← Volver a Menu</a>
        </div>
    </div>
</body>

</html>