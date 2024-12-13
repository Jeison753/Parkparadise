<?php 

// Conección a la base de datos
$conexion = mysqli_connect('localhost', 'root', '', 'parkparadise');

// Si la conección falla bota un error
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}

?>
