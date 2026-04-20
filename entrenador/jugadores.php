<?php

$usuario_sesion = $_SESSION['user'] ?? null;
$identificador_usuario = $usuario_sesion['id'] ?? 0;

$consulta_perfil = "SELECT equipo_id FROM entrenadores WHERE usuario_id = :id";
$stmt_perfil = $pdo->prepare($consulta_perfil);
$stmt_perfil->execute([':id' => $identificador_usuario]);
$datos_entrenador = $stmt_perfil->fetch(PDO::FETCH_ASSOC);

$mi_equipo_id = $datos_entrenador['equipo_id'] ?? 0;
$lista_jugadores = [];


if ($mi_equipo_id > 0) {
    $consulta_jugadores = "
    SELECT 
        jugadores.id,
        jugadores.nombre AS jugador,
        jugadores.edad,
        jugadores.posicion,
        equipos.nombre AS equipo,
        equipos.categoria
    FROM jugadores 
    INNER JOIN equipos ON jugadores.equipo_id = equipos.id
    WHERE jugadores.equipo_id = :mi_id
    ORDER BY jugadores.posicion DESC
    ";
    
    $stmt_jugadores = $pdo->prepare($consulta_jugadores);
    $stmt_jugadores->execute([':mi_id' => $mi_equipo_id]);
    $lista_jugadores = $stmt_jugadores->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
    .jugadoresContenedor {
        padding: 30px;
        font-family: 'Inter', sans-serif;
        color: #374151;
    }

    .jugadoresHeader {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 25px;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header-left {
        flex: 1;
    }

    .jugadoresHeader h2 {
        margin: 0;
        font-size: 24px;
    }

    .jugadoresHeader span {
        color: #64748b;
        font-size: 14px;
    }

    .btnAñadir {
        background: #16a34a;
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        transition: 0.25s;
        white-space: nowrap;
        margin-top: 10px;
        height: fit-content;
        display: inline-block;
    }

    .btnAñadir:hover {
        background: #15803d;
        transform: translateY(-2px);
    }

    #jugadoresGrid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }

    .jugadorCard {
        position: relative;
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        transition: 0.25s;
        text-align: center;
    }

    .jugadorCard:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
    }

    .action-icons {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 6px;
        z-index: 10;
    }

    .editIcon, .deleteIcon {
        color: #9ca3af;
        font-size: 16px;
        text-decoration: none;
        padding: 6px;
        border-radius: 8px;
        transition: 0.2s;
    }

    .editIcon:hover { background: #dbeafe; color: #2563eb; }
    .deleteIcon:hover { background: #fee2e2; color: #dc2626; }

    .avatar {
        font-size: 30px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .jugadorHeader h3 {
        margin: 5px 0 2px;
        font-size: 18px;
        font-weight: 600;
    }

    .posicion {
        font-size: 12px;
        color: #6b7280;
        letter-spacing: .5px;
    }

    .info {
        margin: 12px 0;
        font-size: 14px;
        color: #374151;
    }

    .categoria {
        display: inline-block;
        margin-top: 10px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        color: white;
    }

    /* Colores por categoría */
    .cadete { background-color: #22c55e; }
    .senior { background-color: #3b82f6; }
    .juvenil { background-color: #f59e0b; }
    .infantil { background-color: #ef4444; }

    .aviso-error {
        background: #fee2e2;
        color: #dc2626;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #fecaca;
        text-align: center;
    }
</style>

<div class="jugadoresContenedor">
    
    <?php if ($mi_equipo_id == 0): ?>
        <div class="aviso-error">
            <h3><i class="fa-solid fa-circle-exclamation"></i> Acceso restringido</h3>
            <p>Todavía no tienes un equipo asignado por el administrador. Contacta con tu club para poder gestionar la plantilla.</p>
        </div>
    <?php else: ?>

        <div class="jugadoresHeader">
            <div class="header-left">
                <h2>Mi Plantilla</h2>
                <span>Gestión de jugadores de tu equipo</span><br>
                <span>Total de jugadores: <strong><?= count($lista_jugadores) ?>/25</strong></span>
            </div>
            
            <a href="nuevo_jugador.php" class="btnAñadir">
                <i class="fa-solid fa-plus"></i> Añadir jugador
            </a>
        </div>

        <div id="jugadoresGrid">
            <?php if (empty($lista_jugadores)): ?>
                <p style="grid-column: 1/-1; color: #94a3b8;">No hay jugadores registrados en tu equipo.</p>
            <?php else: ?>
                <?php foreach ($lista_jugadores as $jugador): ?>
                <div class="jugadorCard">
                    <div class="action-icons">
                        <a href="editar_jugador.php?id=<?= $jugador['id'] ?>" class="editIcon" title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="eliminar_jugador.php?id=<?= $jugador['id'] ?>" class="deleteIcon" 
                           onclick="return confirm('¿Eliminar a <?= $jugador['jugador'] ?>?')" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>

                    <div class="jugadorHeader">
                        <i class="fa-regular fa-user avatar"></i>
                        <h3><?= htmlspecialchars($jugador['jugador']) ?></h3>
                        <span class="posicion"><?= strtoupper(htmlspecialchars($jugador['posicion'])) ?></span>
                    </div>

                    <p class="info">
                        <?= htmlspecialchars($jugador['edad']) ?> años<br>
                        <strong><?= htmlspecialchars($jugador['equipo']) ?></strong>
                    </p>

                    <span class="categoria <?= strtolower(htmlspecialchars($jugador['categoria'])) ?>">
                        <?= htmlspecialchars($jugador['categoria']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>