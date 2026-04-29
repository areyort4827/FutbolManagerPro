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
$miId = (int)($_SESSION['user']['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario_submit'])) {
    $usuarioId = (int)($_POST['usuario_id'] ?? 0);

    if ($usuarioId <= 0) {
        $error = 'Usuario no válido.';
    } elseif ($usuarioId === $miId) {
        $error = 'No puedes eliminar tu propia cuenta.';
    } else {
        try {
            $stmtInfo = $pdo->prepare('SELECT id, rol FROM usuarios WHERE id = :id LIMIT 1');
            $stmtInfo->execute([':id' => $usuarioId]);
            $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);

            if (!$info) {
                $error = 'El usuario no existe.';
            } elseif (($info['rol'] ?? '') === 'admin') {
                $error = 'Por seguridad, no se eliminan admins desde aquí.';
            } else {
                $pdo->beginTransaction();

                // Si es entrenador, borrar primero su registro en la tabla entrenadores
                if (($info['rol'] ?? '') === 'entrenador') {
                    $stmtEnt = $pdo->prepare('DELETE FROM entrenadores WHERE usuario_id = :id');
                    $stmtEnt->execute([':id' => $usuarioId]);
                }

                $stmtDel = $pdo->prepare('DELETE FROM usuarios WHERE id = :id LIMIT 1');
                $stmtDel->execute([':id' => $usuarioId]);

                $pdo->commit();
                $success = 'Usuario eliminado correctamente.';
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'Error al eliminar el usuario.';
        }
    }
}

try {
    $stmt = $pdo->query("
        SELECT u.id, u.nombre, u.email, u.rol, c.nombre AS club
        FROM usuarios u
        LEFT JOIN clubes c ON c.id = u.club_id
        ORDER BY u.rol, u.nombre
    ");
    $usuarios = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (PDOException $e) {
    $usuarios = [];
    $error = $error ?: 'Error al cargar usuarios.';
}
?>

<style>
    .users-header {
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:14px;
        margin-bottom:16px;
    }
    .users-title { margin:0; font-size:22px; color:#0f172a; display:flex; align-items:center; gap:10px; }
    .users-title i { color:#16a34a; }
    .users-sub { margin:6px 0 0; color:#64748b; }

    .users-card {
        background:white;
        border-radius:14px;
        padding:16px;
        box-shadow:0 10px 25px rgba(0,0,0,0.08);
        border:1px solid rgba(15, 23, 42, 0.06);
    }

    .msg {
        position: relative;
        padding: 12px 14px;
        border-radius: 12px;
        margin-bottom: 14px;
        color: white;
        font-weight: 600;
    }
    .msg.error { background:#ef4444; }
    .msg.ok { background: linear-gradient(145deg, #22c55e, #16a34a); }
    .msg-close {
        position:absolute;
        top:8px;
        right:10px;
        width:24px;
        height:24px;
        border-radius:8px;
        border:0;
        cursor:pointer;
        background: rgba(255,255,255,0.18);
        color:white;
        font-weight:900;
        line-height:24px;
        text-align:center;
        transition:0.15s;
    }
    .msg-close:hover { background: rgba(255,255,255,0.28); transform: translateY(-1px); }

    .table-wrap { width:100%; overflow:auto; border-radius:12px; }
    table { width:100%; border-collapse: collapse; min-width: 720px; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 14px; }
    th { color:#0f172a; font-size:12px; letter-spacing:0.03em; text-transform:uppercase; background:#f8fafc; }
    tr:hover td { background:#f8fafc; }

    .badge {
        display:inline-flex;
        align-items:center;
        padding:4px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:800;
    }
    .badge.admin { background: rgba(239,68,68,0.12); color:#b91c1c; }
    .badge.entrenador { background: rgba(59,130,246,0.12); color:#1d4ed8; }
    .badge.equipo { background: rgba(245,158,11,0.14); color:#b45309; }
    .badge.jugador { background: rgba(34,197,94,0.12); color:#166534; }

    .actions { display:flex; gap:10px; align-items:center; }
    .btn-danger {
        padding: 10px 12px;
        border-radius: 10px;
        border: 0;
        cursor: pointer;
        font-weight: 900;
        background: rgba(239,68,68,0.12);
        color:#b91c1c;
        transition:0.15s;
    }
    .btn-danger:hover { background: rgba(239,68,68,0.18); transform: translateY(-1px); }
    .muted { color:#64748b; font-size:13px; }
</style>

<div class="users-header">
    <div>
        <h2 class="users-title"><i class="fa-solid fa-user-xmark"></i> Eliminar usuarios</h2>
      
    </div>
</div>

<div class="users-card">
    <?php if ($error): ?>
        <div class="msg error" data-dismissible>
            <button class="msg-close" type="button" aria-label="Cerrar">×</button>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="msg ok" data-dismissible>
            <button class="msg-close" type="button" aria-label="Cerrar">×</button>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Club</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <?php
                        $id = (int)($u['id'] ?? 0);
                        $rol = (string)($u['rol'] ?? '');
                        $esYo = $id === $miId;
                        $esAdmin = $rol === 'admin';
                        $puedeEliminar = !$esYo && !$esAdmin;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars((string)($u['nombre'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($u['email'] ?? '')) ?></td>
                        <td><span class="badge <?= htmlspecialchars($rol) ?>"><?= strtoupper(htmlspecialchars($rol)) ?></span></td>
                        <td><?= htmlspecialchars((string)($u['club'] ?? '—')) ?></td>
                        <td>
                            <div class="actions">
                                <?php if ($esYo): ?>
                                    <span class="muted">Tu cuenta</span>
                                <?php elseif ($esAdmin): ?>
                                    <span class="muted">Protegido</span>
                                <?php else: ?>
                                    <form method="POST" action="" onsubmit="return confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.');">
                                        <input type="hidden" name="eliminar_usuario_submit" value="1">
                                        <input type="hidden" name="usuario_id" value="<?= $id ?>">
                                        <button class="btn-danger" type="submit">Eliminar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.msg-close');
    if (!btn) return;
    const box = btn.closest('[data-dismissible]');
    if (box) box.style.display = 'none';
});
</script>

