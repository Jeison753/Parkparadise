<?php

session_start();

include("conexion.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $nombre = ucfirst(trim($_POST["NOMBRE"]));
    $telefono = trim($_POST["TELEFONO"]);
    $correo = trim($_POST["CORREORE"]);
    $contrasena = trim($_POST["CONTRASENARE"]);
    $documento = trim($_POST["DOCUMENTO"]);

    if (!empty($nombre) && !empty($telefono) && !empty($correo) && !empty($contrasena) && !empty($documento)) {
        
        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, telefono, documento) VALUES (?,?,?,?,?)";
        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssss", $nombre, $correo, $contrasena, $telefono, $documento);
            
            if ($stmt->execute()) {
                echo "Registro exitoso , $nombre";
            } else {
                echo "Error al registrar ". $stmt->error;
            }

            $stmt->close();
        }else{
            echo "Error en la preparacion de la consulta: " . $conexion->error;
        }
        
    }else{
        echo "Todos los campos son obligatorios";
    }
}


$conexion->close();
?>