<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    .accionesJugadores {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
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
    }

    .btnAñadir:hover {
        background: #15803d;
        transform: translateY(-2px);
    }

    /*Pantalla jugadores*/
    #jugadoresGrid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    #jugadorCard {
        background: #dbdbdb;
        border-radius: 14px;
        overflow: hidden;
        color: rgb(0, 0, 0);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        transition: 0.3s;
    }

    #jugadorCard:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
    }

    .jugadorTop {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .nombreInfo h3 {
        margin: 0;
        font-size: 18px;
    }

    .nombreInfo span {
        font-size: 12px;
        opacity: 0.85;
    }

    #jugadorInfo {
        padding: 15px;
    }

    .posicion {
        margin: 0 0 10px;
        color: #20b155;
    }

    #categoria {
        display: inline-block;
        margin-top: 10px;
        padding: 5px 10px;
        background: #22c55e;
        border-radius: 8px;
        font-size: 12px;
        font-weight: bold;
    }

    .btnEliminar {
        width: 30%;
        padding: 8px;
        background: #ef4444;
        color: white;
        border: none;
        float: right;
        border-radius: 6px;
        margin-top: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }

    .jugadoresHeader {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .jugadoresContenedor {
        padding: 30px;
    }
    </style>
</head>

<body>
    <div class="jugadoresContenedor">
        <div class="jugadoresHeader">
            <div>
                <h2>Gestión de Jugadores</h2>
                <span style="color:#64748b">Gestiona tu plantilla</span>
            </div>

            <a href="nuevo_jugador.php" class="btnAñadir">
                + Añadir jugador
            </a>
        </div>

        <div id="jugadoresGrid" class="page">
            <?php
     
        $equipo_id = $_SESSION['equipo_id'];
  $sql = "
        SELECT j.id, j.nombre AS jugador, j.edad, j.posicion,
               e.nombre AS equipo, e.categoria, c.nombre AS club
        FROM jugadores j
        INNER JOIN equipos e ON j.equipo_id = e.id
        INNER JOIN clubes c ON e.equipo_id = c.id
        ORDER BY c.nombre
    ";
    $resultado = $pdo->query($sql);
    $jugadores = $resultado->fetchAll(PDO::FETCH_ASSOC);

    foreach ($jugadores as $fila){
    ?>

            <div id="jugadorCard">
                <div class="jugadorTop">

                    <div class="avatar">
                        <img src="../assets/img/player.png" alt="Jugador">
                    </div>

                    <div class="nombreInfo">
                        <h3><?= htmlspecialchars($fila["jugador"]) ?></h3>
                        <span class="posicion"><?= strtoupper($fila["posicion"]) ?></span>
                    </div>

                </div>

                <div id="jugadorInfo">
                    <p><?= $fila["edad"] ?> años</p>
                    <p><?= $fila["equipo"] ?></p>
                    <span id="categoria"><?= $fila["categoria"] ?></span>
                </div>
                <form action="eliminar_jugador.php" method="POST"
                    onsubmit="return confirm('¿Seguro que quieres eliminar a <?= htmlspecialchars($fila['jugador'], ENT_QUOTES) ?>?')">
                    <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                    <button type="submit" class="btnEliminar">Eliminar</button>
                </form>
            </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>