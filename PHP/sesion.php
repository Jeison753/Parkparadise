<?php
session_start();
header('Content-Type: application/json');

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = isset($_POST["CORREO"]) ? trim($_POST["CORREO"]) : "";
    $contrasena = isset($_POST["CONTRASENA"]) ? trim($_POST["CONTRASENA"]) : "";

    if (empty($correo) || empty($contrasena)) {
        echo json_encode([
            'rta' => 'ERROR',
            'message' => 'Por favor complete todos los campos'
        ]);
        exit();
    }

    // Consulta para verificar el usuario
    $stmt = $conexion->prepare("SELECT nombre, contrasena, documento FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();

        if ($contrasena === $fila['contrasena']) {
            $_SESSION['correo'] = $correo;
            $_SESSION['documento'] = $fila['documento'];
            $_SESSION['nombre'] = $fila['nombre'];
            

            echo json_encode([
                'rta' => 'OK',
                'message' => 'Inicio de sesión exitoso',
                'redirect' => '../HTML/index.html'
            ]);
        } else {
            echo json_encode([
                'rta' => 'ERROR',
                'message' => 'Contraseña incorrecta'
            ]);
        }
    } else {
        echo json_encode([
            'rta' => 'ERROR',
            'message' => 'Correo no registrado'
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'rta' => 'ERROR',
        'message' => 'Método no permitido'
    ]);
}

$conexion->close();
?>
