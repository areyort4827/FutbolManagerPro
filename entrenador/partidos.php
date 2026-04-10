<?php
include("../conexion.php");

if (isset($_POST['guardar'])) {
       $local = $_POST['equipo_local'];
       $visitante = $_POST['equipo_visitante'];
       $fecha = $_POST['fecha'];
       $res = $_POST['resultado'];

       $sql = "INSERT INTO partidos (equipo_local, equipo_visitante, fecha, resultado)
            VALUES ('$local', '$visitante', '$fecha', '$res')";
       $conexion->query($sql);
}

if (isset($_POST['actualizar'])) {
       $id = $_POST['id'];
       $res = $_POST['resultado'];

       $sql = "UPDATE partidos SET resultado='$res' WHERE id=$id";
       $conexion->query($sql);
}

$sql = "SELECT * FROM partidos";
$partidos = $conexion->query($sql);
?>

<div id="partidos-grid">

       <?php while ($fila = $partidos->fetch_assoc()) { ?>

              <div class="partido-card">

                     <div class="partido-equipos">
                            <?= $fila['equipo_local'] ?> vs <?= $fila['equipo_visitante'] ?>
                     </div>

                     <div class="partido-info">
                            Fecha: <?= $fila['fecha'] ?>
                     </div>

                     <div class="resultado">
                            <form method="POST">
                                   <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                                   <input type="text" name="resultado" value="<?= $fila['resultado'] ?>">
                                   <button type="submit" name="actualizar">Guardar</button>
                            </form>
                     </div>

              </div>

       <?php } ?>

</div>

<h2>Añadir Partido</h2>

<form method="POST">

       <input type="text" name="equipo_local" placeholder="Equipo local" required>

       <input type="text" name="equipo_visitante" placeholder="Equipo visitante" required>

       <input type="date" name="fecha" required>

       <input type="text" name="resultado" placeholder="Resultado">

       <button type="submit" name="guardar">Guardar Partido</button>

</form>