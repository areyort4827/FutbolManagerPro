<?php
require_once "../config/conexion.php";
session_start();

$usuario_id = $_SESSION['user']['id'] ?? 0;

$sql = "SELECT equipo_id FROM entrenadores WHERE usuario_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $usuario_id]);
$entrenador = $stmt->fetch(PDO::FETCH_ASSOC);
$mi_equipo_id = $entrenador['equipo_id'] ?? 0;

if ($mi_equipo_id == 0) die("No tienes un equipo asignado.");

$stmtEquipo = $pdo->prepare("SELECT nombre FROM equipos WHERE id = :id");
$stmtEquipo->execute([':id' => $mi_equipo_id]);
$equipo = $stmtEquipo->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nuevo Jugador - FutbolManager Pro</title>
<style>
body { display:flex; justify-content:center; align-items:center; min-height:100vh; background:#ecfdf5; font-family:'Segoe UI',sans-serif; }
.formularioContainer { background:white; padding:40px; border-radius:12px; box-shadow:0 15px 35px rgba(0,0,0,0.12); width:420px; border-top:6px solid #16a34a; }
.formularioContainer h2 { color:#16a34a; text-align:center; margin-bottom:25px; }
.formularioContainer input, .formularioContainer select { width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #d1d5db; font-size:15px; box-sizing:border-box; }
.formularioContainer input:disabled { background:#f3f4f6; color:#6b7280; }
label.campo-label { font-size:13px; color:#6b7280; display:block; margin-bottom:4px; }
.edad-preview { font-size:13px; color:#16a34a; margin-top:-10px; margin-bottom:12px; min-height:18px; }
.formularioContainer button { width:100%; padding:14px; background:#16a34a; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; transition:0.25s; font-size:15px; }
.formularioContainer button:hover { background:#15803d; transform:translateY(-2px); }
.volver { display:block; text-align:center; margin-top:20px; text-decoration:none; color:#16a34a; }
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

        <label class="campo-label">Equipo</label>
        <input type="text" value="<?= htmlspecialchars($equipo['nombre']) ?>" disabled>
        <input type="hidden" name="equipo_id" value="<?= $mi_equipo_id ?>">

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
