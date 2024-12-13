<?php
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parkparadise"; // Base de datos

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$error = ""; // Variable para almacenar mensajes de error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Verificar si el correo está registrado en la base de datos
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Usuario encontrado
        $usuario = $result->fetch_assoc();

        // Enlace para restablecer la contraseña
        $resetLink = "http://localhost:3000/php/Reestablecercontrasena.php?email=" . urlencode($email);

        // Configuración de PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Cambia esto si usas otro proveedor
            $mail->SMTPAuth = true;
            $mail->Username = 'parkparadisetps@gmail.com'; // Nuevo correo
            $mail->Password = 'muuq emaz tmpv enyv'; // Nueva contraseña
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            // Configuración del remitente
            $mail->setFrom('parkparadisetps@gmail.com', 'Park Paradise');
            $mail->addAddress($email); // Destinatario
            $mail->Subject = 'Recuperar Contraseña';
            $mail->isHTML(true);
            
            // Colores para los estilos
            $backgroundColor = '#cccccc'; // Gris claro
            $buttonColor = '#ABDB25'; // Verde
            $textColor = '#666666'; // Gris oscuro
            $linkColor = '#FFFFFF'; // Blanco

            $mail->Body = "
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            color: $textColor;
                            margin: 0;
                            padding: 0;
                            background-color: $backgroundColor;
                        }
                        .email-container {
                            max-width: 600px;
                            margin: 20px auto;
                            padding: 20px;
                            background-color: #ffffff;
                            border-radius: 8px;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                            text-align: center;
                        }
                        .email-header {
                            font-size: 24px;
                            font-weight: bold;
                            color: $buttonColor;
                        }
                        .email-body {
                            text-align: left;
                            margin-top: 20px;
                        }
                        .email-footer {
                            margin-top: 20px;
                            font-size: 12px;
                            color: $textColor;
                        }
                        .btn {
                            display: inline-block;
                            margin-top: 20px;
                            padding: 10px 20px;
                            background-color: $buttonColor;
                            color: $linkColor;
                            text-decoration: none;
                            border-radius: 5px;
                            font-size: 16px;
                        }
                        .btn:hover {
                            background-color: $linkColor;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='email-header'>Restablecimiento de Contraseña</div>
                        <div class='email-body'>
                            <p>Hola <strong>{$usuario['nombre']}</strong>,</p>
                            <p>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el botón de abajo para continuar:</p>
                            <a href='$resetLink' class='btn'>Restablecer Contraseña</a>
                            <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
                        </div>
                        <div class='email-footer'>
                            <p>© 2024 Park Paradise. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
        
            // Enviar el correo
            $mail->send();
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Correo enviado',
                        text: 'Por favor revisa tu bandeja de entrada.',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/index.html';
                        }
                    });
                });
            </script>";
        } catch (Exception $e) {
            $error = "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        $error = "El correo no está registrado en nuestra base de datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="/CSS/Styles.css">
    <link rel="stylesheet" href="/CSS/Recuperar.css">
</head> 

<body class="body-olvidar">
    <div class="container-olvide mt-5">
        <h2 class="text-olvidar">Recuperar Contraseña</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form id="recuperar-form" action="" method="POST" class="recuperar-form mt-4">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="email" placeholder="Ingresa tu correo" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</body>
</html>