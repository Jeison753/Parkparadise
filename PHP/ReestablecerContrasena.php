<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parkparadise"; // Base de datos

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        die("Las contraseñas no coinciden.");
    }

    // Verificar si el correo electrónico existe en la base de datos
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizar la contraseña del usuario con el valor ingresado
        $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE correo = ?");
        $stmt->bind_param("ss", $password, $email);

        if ($stmt->execute()) {
            echo "Contraseña actualizada exitosamente.";
        } else {
            echo "Error al actualizar la contraseña.";
        }
    } else {
        echo "Correo electrónico no encontrado.";
    }
       
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<link rel="stylesheet" href="/CSS/Reestablecer.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
</head>
<body>
    <div class="container">
        <h1>Cambiar Contraseña</h1>
        <form method="POST">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            <br><br>

            <label for="password">Nueva Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <br><br>

            <label for="password_confirm">Confirmar Contraseña:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
            <br><br>

            <button type="submit">Cambiar Contraseña</button>
        </form>
    </div>
</body>
</html>