<?php
session_start();
require_once "../config/conexion.php";

$club_id = $_SESSION['club_id'] ?? null;
$esAdminGlobal = empty($club_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nuevo Jugador - FutbolManager Pro</title>
<style>
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #ecfdf5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.formularioContainer {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    width: 400px;
    border-top: 6px solid #16a34a;
}

.formularioContainer h2 {
    color: #16a34a;
    text-align: center;
    margin-bottom: 25px;
}

.formularioContainer input,
.formularioContainer select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 15px;
}

.formularioContainer button {
    width: 100%;
    padding: 14px;
    background: #16a34a;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.25s;
}

.formularioContainer button:hover {
    background: #15803d;
    transform: translateY(-2px);
}

.volver {
    display: block;
    text-align: center;
    margin-top: 20px;
    text-decoration: none;
    color: #16a34a;
}
.volver:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="formularioContainer">
    <h2>Nuevo Jugador</h2>

    <form action="guardar_jugador.php" method="POST">

        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="number" name="edad" placeholder="Edad" required>

           <select name="posicion" required>
            <option value="">Seleccionar posición</option>
            <option value="delantero">Delantero</option>
            <option value="mediocentro">Mediocentro</option>
            <option value="defensa">Defensa</option>
            <option value="portero">Portero</option>
        </select>

        <select name="equipo_id" required>
            <option value="">Seleccionar equipo</option>
            <?php
            if ($esAdminGlobal) {
                $equipos = $pdo->query("SELECT id, nombre FROM equipos ORDER BY nombre ASC");
            } else {
                $stmtEquipos = $pdo->prepare("SELECT id, nombre FROM equipos WHERE equipo_id = :club_id ORDER BY nombre ASC");
                $stmtEquipos->execute([':club_id' => $club_id]);
                $equipos = $stmtEquipos;
            }

            while($e = $equipos->fetch(PDO::FETCH_ASSOC)){
                echo "<option value='{$e['id']}'>{$e['nombre']}</option>";
            }
            ?>
        </select>

        <button type="submit">Guardar jugador</button>
    </form>

    <a class="volver" href="menu.php">← Volver a Menu</a>
</div>

</body>
</html>
