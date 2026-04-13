<?php
session_start();
require_once 'config/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $usuario = verificarLogin($email, $password);

    if ($usuario) {
        $_SESSION['user'] = $usuario;
        $user = $_SESSION['user'];

        $_SESSION['club_id'] = $user['club_id']; // PARA SABER A QUE CLUB PERTENECE EL USUARIO

        $rol = $user['rol']; 

        if ($rol === 'admin'){
            header("Location: admin/menu.php");

        } elseif ($rol === 'entrenador'){
            header("Location: entrenador/menu.php");

        } elseif ($rol === 'equipo'){
            
            header("Location: equipo/menu.php");

        } else {
            header("Location: jugador/menu.php");
        }
        exit;

    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FutbolManager Pro - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #ffffff;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1f2937;
    }

    /* CONTENEDOR GENERAL */
    .contenedor {
        width: 100%;
        max-width: 450px;
        padding: 40px;
        text-align: center;
    }

    /* TITULO */
    .logo-login {
        font-size: 2.5rem;
        font-weight: 700;
        color: #16a34a;
        margin-bottom: 5px;
    }

    .subtitle {
        color: #6b7280;
        margin-bottom: 35px;
    }

    /* INPUTS */
    .input {
        margin-bottom: 20px;
        text-align: left;
    }

    .input label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #374151;
    }

    .input input {
        width: 100%;
        padding: 14px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        font-size: 1rem;
        transition: 0.25s;
    }

    .input input:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22,163,74,0.15);
    }

    /* BOTONES */
    .btn-login {
        width: 100%;
        padding: 14px;
        background: #16a34a;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.25s;
    }

    .btn-login:hover {
        background: #15803d;
    }

    .btn-registrarse {
        width: 100%;
        padding: 14px;
        background: white;
        color: #16a34a;
        border: 2px solid #16a34a;
        border-radius: 8px;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 12px;
        transition: 0.25s;
    }

    .btn-registrarse:hover {
        background: #16a34a;
        color: white;
    }

    /* ERROR */
    .error-message {
        background: #ef4444;
        color: white;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    /* DEMO USERS */
    .demo {
        margin-top: 35px;
        font-size: 0.9rem;
        color: #6b7280;
        line-height: 1.6;
    }
</style>

</head>
<body>

<div class="contenedor">
    <h1 class="logo-login">FutbolManager Pro</h1>
    <p class="subtitle">Accede a tu panel de gestión</p>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

   <form method="POST" action="">
    <div class="input">
        <label>Email</label>
        <input type="email" name="email" required autofocus>
    </div>

    <div class="input">
        <label>Contraseña</label>
        <input type="password" name="password" required>
    </div>

    <button type="submit" class="btn-login">Iniciar Sesión</button>
    <button type="submit" class="btn-registrarse">Registrarse</button>
</form>

</div>

</body>
</html>