<?php
session_start();
require_once __DIR__ . '/config/conexion.php';

$clubsStmt = $pdo->query('SELECT id, nombre FROM clubes ORDER BY nombre');
$clubs = $clubsStmt ? $clubsStmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Equipos agrupados por club_id para el JS
$equiposStmt = $pdo->query('SELECT id, nombre, categoria, equipo_id AS club_id FROM equipos ORDER BY nombre');
$equiposPorClub = [];
foreach ($equiposStmt->fetchAll(PDO::FETCH_ASSOC) as $eq) {
    $equiposPorClub[$eq['club_id']][] = $eq;
}

$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro - FutbolManager Pro</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; }
body { min-height:100vh; background:linear-gradient(135deg,#ffffff,#ecfdf5); display:flex; align-items:center; justify-content:center; padding:30px 16px; }
.registro-container { width:100%; max-width:500px; background:white; padding:40px; border-radius:14px; box-shadow:0 15px 35px rgba(0,0,0,0.12); border-top:6px solid #16a34a; }
.registro-container h2 { text-align:center; color:#16a34a; margin-bottom:25px; font-size:28px; }
.input-group { margin-bottom:16px; }
.input-group input, .input-group select { width:100%; padding:14px; border-radius:8px; border:1px solid #d1d5db; font-size:15px; transition:0.2s; }
.input-group input:focus, .input-group select:focus { outline:none; border-color:#16a34a; box-shadow:0 0 0 3px rgba(22,163,74,0.2); }
.password-wrapper { position:relative; }
.password-wrapper input { padding-right:48px; }
.toggle-pass { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#6b7280; font-size:18px; padding:4px; }
.toggle-pass:hover { color:#16a34a; }
.pass-error { font-size:13px; color:#dc2626; margin-top:4px; display:none; }
#grupo-equipo { display:none; }
.btn-register { width:100%; padding:14px; background:#16a34a; border:none; border-radius:8px; color:white; font-size:16px; font-weight:bold; cursor:pointer; transition:0.25s; margin-top:8px; }
.btn-register:hover { background:#15803d; transform:translateY(-2px); box-shadow:0 8px 18px rgba(22,163,74,0.35); }
.volver { display:block; text-align:center; margin-top:20px; text-decoration:none; color:#16a34a; font-weight:600; }
.volver:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="registro-container">
<h2>Crear cuenta</h2>

<?php if ($flashError): ?>
    <div style="background:#ef4444;color:white;padding:12px;border-radius:8px;margin-bottom:18px;">
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<form action="procesar_registro.php" method="POST" id="formRegistro">

    <div class="input-group">
        <input type="text" name="nombre" placeholder="Nombre completo" required>
    </div>

    <div class="input-group">
        <input type="email" name="email" placeholder="Email" required>
    </div>

    <div class="input-group">
        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Contraseña" required>
            <button type="button" class="toggle-pass" onclick="togglePass('password',this)" title="Mostrar contraseña">👁</button>
        </div>
    </div>

    <div class="input-group">
        <div class="password-wrapper">
            <input type="password" name="password2" id="password2" placeholder="Repetir contraseña" required>
            <button type="button" class="toggle-pass" onclick="togglePass('password2',this)" title="Mostrar contraseña">👁</button>
        </div>
        <div class="pass-error" id="passError">Las contraseñas no coinciden.</div>
    </div>

    <div class="input-group">
        <select name="rol" id="rolSelect">
            <option value="jugador">Jugador</option>
            <option value="entrenador">Entrenador</option>
            <option value="equipo">Equipo</option>
        </select>
    </div>

    <div class="input-group">
        <select name="club_id" id="clubSelect" required>
            <option value="">Selecciona un club</option>
            <?php foreach ($clubs as $club): ?>
                <option value="<?= (int)$club['id'] ?>"><?= htmlspecialchars($club['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="input-group" id="grupo-equipo">
        <select name="equipo_id" id="equipoSelect">
            <option value="">Selecciona un equipo</option>
        </select>
    </div>

    <button type="submit" class="btn-register">Registrarse</button>
</form>

<a class="volver" href="index.php">← Volver al login</a>
</div>

<script>
const equiposPorClub = <?= json_encode($equiposPorClub) ?>;

function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? '👁' : '🙈';
}

const pass1       = document.getElementById('password');
const pass2       = document.getElementById('password2');
const passError   = document.getElementById('passError');
const rolSelect   = document.getElementById('rolSelect');
const clubSelect  = document.getElementById('clubSelect');
const grupoEquipo = document.getElementById('grupo-equipo');
const equipoSelect = document.getElementById('equipoSelect');

function checkPasswords() {
    if (pass2.value && pass1.value !== pass2.value) {
        passError.style.display = 'block';
        pass2.style.borderColor = '#dc2626';
    } else {
        passError.style.display = 'none';
        pass2.style.borderColor = '#d1d5db';
    }
}
pass1.addEventListener('input', checkPasswords);
pass2.addEventListener('input', checkPasswords);

document.getElementById('formRegistro').addEventListener('submit', function(e) {
    if (pass1.value !== pass2.value) {
        e.preventDefault();
        passError.style.display = 'block';
        pass2.focus();
    }
});

function actualizarEquipos() {
    const clubId = parseInt(clubSelect.value);
    const esEntrenador = rolSelect.value === 'entrenador';
    grupoEquipo.style.display = esEntrenador ? 'block' : 'none';
    equipoSelect.required = esEntrenador;
    equipoSelect.innerHTML = '<option value="">Selecciona un equipo</option>';
    if (esEntrenador && clubId && equiposPorClub[clubId]) {
        equiposPorClub[clubId].forEach(function(eq) {
            const opt = document.createElement('option');
            opt.value = eq.id;
            opt.textContent = eq.nombre + ' (' + eq.categoria + ')';
            equipoSelect.appendChild(opt);
        });
    }
}
rolSelect.addEventListener('change', actualizarEquipos);
clubSelect.addEventListener('change', actualizarEquipos);
</script>

</body>
</html>
