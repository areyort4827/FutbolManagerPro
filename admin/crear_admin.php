<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/conexion.php';

if (!isset($_SESSION['user']) || (($_SESSION['user']['rol'] ?? '') !== 'admin')) {
    echo '<div style="background:#ef4444;color:white;padding:14px;border-radius:12px;">Acceso denegado.</div>';
    return;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_admin_submit'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nombre === '' || $email === '' || $password === '') {
        $error = 'Rellena todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido.';
    } else {
        try {
            $stmtExiste = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
            $stmtExiste->execute([':email' => $email]);
            if ($stmtExiste->fetchColumn()) {
                $error = 'Ya existe una cuenta con ese email.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                if ($passwordHash === false) {
                    $error = 'No se pudo procesar la contraseña.';
                } else {
                    $stmt = $pdo->prepare(
                        'INSERT INTO usuarios (nombre, email, password, rol, club_id)
                         VALUES (:nombre, :email, :password, :rol, :club_id)'
                    );
                    $stmt->execute([
                        ':nombre' => $nombre,
                        ':email' => $email,
                        ':password' => $passwordHash,
                        ':rol' => 'admin',
                        ':club_id' => null,
                    ]);
                    $success = 'Admin creado correctamente.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Error al crear el admin.';
        }
    }
}
?>

<style>
    .crear-admin-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .crear-admin-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
        font-size: 22px;
        color: #0f172a;
    }

    .crear-admin-title i {
        color: #16a34a;
        font-size: 22px;
    }

    .crear-admin-sub {
        margin: 6px 0 0;
        color: #64748b;
    }

    .crear-admin-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 18px;
    }

    .card {
        background: white;
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(15, 23, 42, 0.06);
    }

    .card h3 {
        margin: 0 0 12px;
        color: #0f172a;
        font-size: 16px;
    }

    .msg {
        padding: 12px 14px;
        border-radius: 12px;
        margin-bottom: 14px;
        color: white;
        font-weight: 600;
    }

    .msg.error { background: #ef4444; }
    .msg.ok { background: linear-gradient(145deg, #22c55e, #16a34a); }

    .field {
        margin-bottom: 12px;
    }

    .field label {
        display: block;
        margin-bottom: 6px;
        color: #374151;
        font-weight: 600;
        font-size: 13px;
    }

    .field input {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        font-size: 14px;
        transition: 0.2s;
    }

    .field input:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
    }

    .btn-primary {
        width: 100%;
        padding: 12px 14px;
        border-radius: 12px;
        border: none;
        background: linear-gradient(145deg, #22c55e, #16a34a);
        color: white;
        font-weight: 800;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 20px rgba(22,163,74,0.25);
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 999px;
        background: rgba(34, 197, 94, 0.12);
        color: #166534;
        font-weight: 700;
        font-size: 12px;
    }

    .hint {
        margin: 12px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.4;
    }

    @media (max-width: 900px) {
        .crear-admin-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="crear-admin-header">
    <div>
        <h2 class="crear-admin-title"><i class="fa-solid fa-user-shield"></i> Crear Admin</h2>
    </div>
    <div class="pill">
        <i class="fa-solid fa-lock"></i> Solo admins
    </div>
</div>

<div class="crear-admin-grid">
    <div class="card">
        <h3>Datos del nuevo admin</h3>

        <?php if ($error): ?>
            <div class="msg error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="msg ok"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="crear_admin_submit" value="1">
            <div class="field">
                <label>Nombre</label>
                <input type="text" name="nombre" placeholder="Ej: Admin Secundario" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" placeholder="admin2@correo.com" required>
            </div>
            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Contraseña segura" required>
            </div>
            <button class="btn-primary" type="submit">Crear Admin</button>
        </form>

        <p class="hint">La contraseña se guardará encriptada.</p>
    </div>
</div>
