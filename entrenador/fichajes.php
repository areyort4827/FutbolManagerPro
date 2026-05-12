<?php

$usuario_sesion = $_SESSION['user'] ?? null;
$identificador_usuario = $usuario_sesion['id'] ?? 0;

// Obtener equipo del entrenador
$stmt_perfil = $pdo->prepare("SELECT equipo_id FROM entrenadores WHERE usuario_id = :id");
$stmt_perfil->execute([':id' => $identificador_usuario]);
$datos_entrenador = $stmt_perfil->fetch(PDO::FETCH_ASSOC);
$mi_equipo_id = $datos_entrenador['equipo_id'] ?? 0;

// Jugadores sin equipo — misma lógica que admin jugadores con filtro "Sin equipo"
$stmt_libres = $pdo->prepare("
    SELECT j.id, j.nombre, j.posicion,
           TIMESTAMPDIFF(YEAR, j.fecha_nacimiento, CURDATE()) AS edad
    FROM jugadores j
    LEFT JOIN equipos e ON j.equipo_id = e.id
    LEFT JOIN clubes c ON e.equipo_id = c.id
    WHERE j.equipo_id IS NULL
    ORDER BY j.posicion, j.nombre
");
$stmt_libres->execute();
$jugadores_libres = $stmt_libres->fetchAll(PDO::FETCH_ASSOC);

// Mensaje de feedback
$mensaje = $_SESSION['fichaje_msg'] ?? null;
unset($_SESSION['fichaje_msg']);
?>

<style>
.fichajes-contenedor {
    padding: 30px;
    font-family: 'Inter', sans-serif;
    color: #374151;
}

.fichajes-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 12px;
}

.fichajes-header h2 {
    margin: 0 0 4px;
    font-size: 24px;
}

.fichajes-header span {
    color: #64748b;
    font-size: 14px;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.aviso-error {
    background: #fee2e2;
    color: #dc2626;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #fecaca;
    text-align: center;
}

#fichajesGrid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 20px;
}

.fichajeCard {
    background: #fff;
    border-radius: 14px;
    padding: 22px 20px 18px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    transition: 0.25s;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.fichajeCard:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.12);
}

.fichaje-avatar {
    font-size: 34px;
    color: #94a3b8;
    margin-bottom: 4px;
}

.fichaje-nombre {
    font-size: 17px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.fichaje-posicion {
    font-size: 11px;
    color: #6b7280;
    letter-spacing: .5px;
    text-transform: uppercase;
    margin: 0;
}

.fichaje-edad {
    font-size: 13px;
    color: #374151;
}

.badge-libre {
    display: inline-block;
    background: #fef3c7;
    color: #92400e;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 600;
    margin: 4px 0 8px;
}

.btn-fichar {
    background: #16a34a;
    color: white;
    border: none;
    padding: 10px 22px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
    width: 100%;
    margin-top: 6px;
}

.btn-fichar:hover {
    background: #15803d;
    transform: translateY(-1px);
}

.no-jugadores {
    grid-column: 1 / -1;
    text-align: center;
    color: #94a3b8;
    padding: 50px 0;
    font-size: 15px;
}

.no-jugadores i {
    font-size: 40px;
    display: block;
    margin-bottom: 12px;
    color: #cbd5e1;
}
</style>

<div class="fichajes-contenedor">

    <div class="fichajes-header">
        <div>
            <h2><i class="fa-solid fa-handshake" style="color:#16a34a;"></i> Fichajes</h2>
            <span>Jugadores libres disponibles para fichar</span>
        </div>
        <span style="font-size:13px; color:#64748b;">
            <?= count($jugadores_libres) ?> jugador<?= count($jugadores_libres) !== 1 ? 'es' : '' ?> disponible<?= count($jugadores_libres) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert-<?= $mensaje['tipo'] ?>">
            <i class="fa-solid fa-<?= $mensaje['tipo'] === 'success' ? 'circle-check' : 'circle-exclamation' ?>"></i>
            <?= htmlspecialchars($mensaje['texto']) ?>
        </div>
    <?php endif; ?>

    <?php if ($mi_equipo_id == 0): ?>
        <div class="aviso-error">
            <h3><i class="fa-solid fa-circle-exclamation"></i> Acceso restringido</h3>
            <p>No tienes un equipo asignado. No puedes realizar fichajes.</p>
        </div>
    <?php else: ?>

        <div id="fichajesGrid">
            <?php if (empty($jugadores_libres)): ?>
                <div class="no-jugadores">
                    <i class="fa-solid fa-user-slash"></i>
                    No hay jugadores libres en el sistema en este momento.
                </div>
            <?php else: ?>
                <?php foreach ($jugadores_libres as $j): ?>
                <div class="fichajeCard">
                    <div class="fichaje-avatar">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <p class="fichaje-nombre"><?= htmlspecialchars($j['nombre']) ?></p>
                    <p class="fichaje-posicion"><?= htmlspecialchars($j['posicion']) ?></p>
                    <p class="fichaje-edad"><?= $j['edad'] ?> años</p>
                    <span class="badge-libre">Agente libre</span>
                    <form method="POST" action="fichar_jugador.php">
                        <input type="hidden" name="jugador_id" value="<?= $j['id'] ?>">
                        <button type="submit" class="btn-fichar"
                                onclick="return confirm('¿Fichar a <?= htmlspecialchars($j['nombre'], ENT_QUOTES) ?> para tu equipo?')">
                            <i class="fa-solid fa-plus"></i> Fichar
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>
