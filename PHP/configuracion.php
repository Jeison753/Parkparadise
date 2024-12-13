<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['correo'])) {
    echo json_encode(["success" => false, "message" => "Usuario no autenticado."]);
    exit();
}

// Conexión a la base de datos
include("conexion.php");

// Obtener los datos del JSON recibido
$input = json_decode(file_get_contents("php://input"), true);

if ($input) {
    // Variables necesarias
    $correoSesion = $_SESSION['correo']; // Correo del usuario autenticado
    $nombre = isset($input['nombre']) ? ucfirst(trim($input['nombre'])) : "";
    $nuevoCorreo = isset($input['correo']) ? trim($input['correo']) : "";
    $nuevaContrasena = isset($input['contrasena']) ? trim($input['contrasena']) : "";
    $telefono = isset($input['telefono']) ? trim($input['telefono']) : "";
    $identificacion = isset($input['documento']) ? trim($input['documento']) : "";

    // Validar campos obligatorios
    if (empty($nombre) || empty($nuevoCorreo)) {
        echo json_encode(["success" => false, "message" => "Nombre y correo son obligatorios."]);
        exit();
    }

    try {
        // Preparar la consulta para actualizar los datos del usuario
        if (!empty($nuevaContrasena)) {
            $sql = "UPDATE usuarios 
                    SET nombre = ?, correo = ?, contrasena = ?, telefono = ?, documento = ?
                    WHERE correo = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssss", $nombre, $nuevoCorreo, $nuevaContrasena, $telefono, $identificacion, $correoSesion);
        } else {
            $sql = "UPDATE usuarios 
                    SET nombre = ?, correo = ?, telefono = ?, documento = ?
                    WHERE correo = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssss", $nombre, $nuevoCorreo, $telefono, $identificacion, $correoSesion);
        }

        // Ejecutar la consulta
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Si la consulta afectó algún registro
                $_SESSION['correo'] = $nuevoCorreo; // Actualizar la sesión con el nuevo correo
                echo json_encode(["success" => true, "message" => "Perfil actualizado exitosamente."]);
            } else {
                // Si no se afectaron registros, puede que el correo no coincida
                echo json_encode(["success" => false, "message" => "No se encontró el usuario a modificar."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al ejecutar la consulta."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    } finally {
        $stmt->close(); // Cerrar la consulta
        $conexion->close(); // Cerrar la conexión
    }
} else {
    echo json_encode(["success" => false, "message" => "Datos inválidos."]);
}
?>
