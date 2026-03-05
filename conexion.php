<?php

$conexion = new mysqli("localhost","root","","futbolmanagerpro");

if($conexion->connect_error){
    die("Error de conexión");
}

?>