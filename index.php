<?php
session_start();
require_once 'config/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $usuario = verificarLogin($username, $password);

    if ($usuario) {
        $_SESSION['user'] = $usuario;
        $user = $_SESSION['user'];

        $role = $user['role'];

        if ($role === 'admin'){
        header("Location: admin/dashboard.php");

        }elseif($role === 'entrenador'){
        header("Location: entrenador/dashboard.php");
        }else{
        header("Location: jugador/dashboard.php");
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
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .contenedor {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 50px 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(34, 197, 94, 0.3);
            text-align: center;
            color: white;
        }

        .logo-login {
            font-size: 2.8rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #22c55e;
        }

        .subtitle {
            color: #94a3b8;
            margin-bottom: 35px;
            font-size: 1.1rem;
        }

        .input {
            margin-bottom: 22px;
            text-align: left;
        }

        .input label {
            display: block;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .input input {
            width: 100%;
            padding: 16px 18px;
            border: none;
            border-radius: 12px;
            background: rgba(255,255,255,0.08);
            color: white;
            font-size: 1.05rem;
        }

        .input input:focus {
            outline: none;
            background: rgba(34, 197, 94, 0.15);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.4);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(90deg, #22c55e, #16a34a);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.15rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(34, 197, 94, 0.4);
        }

        .error-message {
            background: #ff0808;
            color: white;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .demo {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <h1 class="logo-login">FutbolManager Pro</h1>
    <p class="subtitle">Gestión Profesional de Equipos de Fútbol</p>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input">
            <label>Usuario</label>
            <input type="text" name="username" required autofocus>
        </div>

        <div class="input">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn-login">Iniciar Sesión</button>
    </form>

    <div class="demo">
        <strong>Usuarios de prueba:</strong><br><br>
        <strong style="color:#22c55e;">Admin:</strong> admin / admin123<br>
        <strong style="color:#3b82f6;">Equipo:</strong> entrenador / equipo123<br>
        <strong style="color:#eab308;">Jugador:</strong> jugador / jugador123
    </div>
</div>

</body>
</html>