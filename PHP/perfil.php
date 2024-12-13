<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['correo'])) {
    var_dump($user);
    echo json_encode([
        'rta' => 'ERROR',
        'message' => 'Usuario no autenticado'
    ]);
    exit();
}

include("conexion.php");

$correo = $_SESSION['correo'];
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'rta' => 'OK',
        'user' => $user
    ]);
} else {
    echo json_encode([
        'rta' => 'ERROR',
        'message' => 'Usuario no encontrado'
    ]);
}

$stmt->close();
$conexion->close();
?>
