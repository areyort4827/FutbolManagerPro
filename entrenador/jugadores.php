<h1>Jugadores</h1>
<div id="jugadores-grid" class="page">
    <?php
    include "../conexion.php";

    $equipo_id = $_SESSION['user']['equipo_id'];

    $sql = "SELECT jugadores.nombre AS jugador, jugadores.edad, jugadores.posicion, 
        equipos.nombre AS equipo, equipos.categoria
        FROM jugadores 
        INNER JOIN equipos ON jugadores.equipo_id = equipos.id
        WHERE jugadores.equipo_id = $equipo_id";

    $resultado = $conexion->query($sql);

    while ($fila = $resultado->fetch_assoc()) {
        ?>
        <div id="jugador-card">
            <div class="jugador-top">

                <div class="avatar">
                    <img src="../assets/img/player.png" alt="Jugador">
                </div>

                <div class="nombre-info">
                    <h3><?= htmlspecialchars($fila["jugador"]) ?></h3>
                    <span class="posicion"><?= strtoupper($fila["posicion"]) ?></span>
                </div>

            </div>

            <div id="jugador-info">
                <p><?= $fila["edad"] ?> años</p>
                <p><?= $fila["equipo"] ?></p>
                <span id="categoria"><?= $fila["categoria"] ?></span>
            </div>

        </div>
    <?php } ?>
</div>