 <div class="card">Información de partidos...</div>

 <?php
       include("../conexion.php");

       $sql = "SELECT * FROM partidos";
       $resultado = $conexion->query($sql);


       if (isset($_POST['guardar'])) {
       $local = $_POST['equipo_local'];
       $visitante = $_POST['equipo_visitante'];
       $fecha = $_POST['fecha'];
       $resultado = $_POST['resultado'];

       $sql = "INSERT INTO partidos (equipo_local, equipo_visitante, fecha, resultado)
            VALUES ('$local', '$visitante', '$fecha', '$resultado')";

       if ($conexion->query($sql) === TRUE) {
              echo "Partido guardado correctamente";
       } else {
              echo "Error: " . $conexion->error;
       }
}

       ?>





<div id="partidos-grid">

<?php
while($fila = $resultado->fetch_assoc()){
    echo "<div class='partido-card'>";
    
    echo "<div class='partido-equipos'>";
    echo $fila['equipo_local'] . " vs " . $fila['equipo_visitante'];
    echo "</div>";
    
    echo "<div class='partido-info'> Fecha: " . $fila['fecha'] . "</div>";
    
    echo "<div class='resultado'>";
    echo $fila['resultado'];
    echo "</div>";
    
    echo "</div>";
}
?>

</div>