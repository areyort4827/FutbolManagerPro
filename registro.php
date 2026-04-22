<?php
session_start();
require_once __DIR__ . '/config/conexion.php';

$clubsStmt = $pdo->query('SELECT id, nombre FROM clubes ORDER BY nombre');
$clubs = $clubsStmt ? $clubsStmt->fetchAll(PDO::FETCH_ASSOC) : [];

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

*{
    box-sizing: border-box;
    margin:0;
    padding:0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body{
    height:100vh;
    background: linear-gradient(135deg,#ffffff,#ecfdf5);
    display:flex;
    align-items:center;
    justify-content:center;
}

.registro-container{
    width:100%;
    max-width:500px;
    background:white;
    padding:40px;
    border-radius:14px;
    box-shadow:0 15px 35px rgba(0,0,0,0.12);
    border-top:6px solid #16a34a;
}

.registro-container h2{
    text-align:center;
    color:#16a34a;
    margin-bottom:25px;
    font-size:28px;
}

.input-group{
    margin-bottom:18px;
}

.input-group input,
.input-group select{
    width:100%;
    padding:14px;
    border-radius:8px;
    border:1px solid #d1d5db;
    font-size:15px;
    transition:0.2s;
}

/* FOCUS */
.input-group input:focus,
.input-group select:focus{
    outline:none;
    border-color:#16a34a;
    box-shadow:0 0 0 3px rgba(22,163,74,0.2);
}

.btn-register{
    width:100%;
    padding:14px;
    background:#16a34a;
    border:none;
    border-radius:8px;
    color:white;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.25s;
}

.btn-register:hover{
    background:#15803d;
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(22,163,74,0.35);
}

.volver{
    display:block;
    text-align:center;
    margin-top:20px;
    text-decoration:none;
    color:#16a34a;
    font-weight:600;
}

.volver:hover{
    text-decoration:underline;
}


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

<form action="procesar_registro.php" method="POST">

    <div class="input-group">
        <input type="text" name="nombre" placeholder="Nombre" required>
    </div>

    <div class="input-group">
        <input type="text" name="apellidos" placeholder="Apellido1 Apellido2" required>
    </div>

    <div class="input-group">
        <input type="email" name="email" placeholder="Email" required>
    </div>

    <div class="input-group">
        <input type="password" name="password" placeholder="Contraseña" required>
    </div>

    <div class="input-group">
        <select name="rol">
            <option value="jugador">Jugador</option>
            <option value="entrenador">Entrenador</option>
            <option value="equipo">Equipo</option>
        </select>
    </div>

    <div class="input-group">
        <select name="club_id" required>
            <option value="">Selecciona un club</option>
            <?php foreach ($clubs as $club): ?>
                <option value="<?= (int)$club['id'] ?>"><?= htmlspecialchars($club['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn-register">
        Registrarse
    </button>

</form>

<a class="volver" href="index.php">← Volver al login</a>

</div>

</body>
</html>
