<?php
session_start();
require_once "../config/conexion.php";

// Obtenemos el club_id del usuario desde la sesión
$club_id = $_SESSION['user']['club_id'] ?? 0;
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
    min-height: 100vh;
    background: #ecfdf5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.formularioContainer {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    width: 420px;
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
    box-sizing: border-box;
}
.edad-preview {
    font-size: 13px;
    color: #16a34a;
    margin-top: -10px;
    margin-bottom: 12px;
    padding-left: 4px;
    min-height: 18px;
}
label.campo-label {
    font-size: 13px;
    color: #6b7280;
    display: block;
    margin-bottom: 4px;
    padding-left: 2px;
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
    font-size: 15px;
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

        <input type="text" name="nombre" placeholder="Nombre completo" required>

        <label class="campo-label">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
               max="<?= date('Y-m-d') ?>" required>
        <div class="edad-preview" id="edad_preview"></div>

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
            // Solo intentamos la consulta si tenemos un club_id válido
            if ($club_id > 0) {
                $stmt = $pdo->prepare("SELECT id, nombre FROM equipos WHERE equipo_id = :club_id ORDER BY nombre");
                $stmt->execute([':club_id' => $club_id]);
                
                while($e = $stmt->fetch(PDO::FETCH_ASSOC)){
                    echo "<option value='{$e['id']}'>" . htmlspecialchars($e['nombre']) . "</option>";
                }
            }
            ?>
        </select>

        <button type="submit">Guardar jugador</button>
    </form>
    <a class="volver" href="menu.php">← Volver al Menú</a>
</div>

<script>
function calcularEdad(fechaStr) {
    if (!fechaStr) return '';
    const hoy = new Date();
    const nac = new Date(fechaStr);
    let edad = hoy.getFullYear() - nac.getFullYear();
    const m = hoy.getMonth() - nac.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) edad--;
    return edad >= 0 ? edad + ' años' : '';
}

document.getElementById('fecha_nacimiento').addEventListener('change', function() {
    document.getElementById('edad_preview').textContent =
        this.value ? '→ Edad actual: ' + calcularEdad(this.value) : '';
});
</script>
</body>
</html>
