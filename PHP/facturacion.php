

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación - Reservas Estacionamiento</title>
    <link rel="stylesheet" href="/Css/styleFactura.css">
    <link rel="stylesheet" href="/Css/stylePrincipal.css">
</head>
<body>

<div class="menu">
        <img src="/assets/menu.png">
        <img src="/assets/ocultar.png">
    </div>

    <div class="barraLateral">

        <div>
            <div class="nombrePagina">
                <img src="/assets/iconLogoParadise.ico" alt="imagen" class="imagen" id="icon">
                <span>PARKPARADISE</span>
                
            </div>
            
        </div>
        <span class="nombreUsu" id="nombreUsu"></span>

        <div>
            <nav class="navegacion">
                <li>
                    <a href="/HTML/index.html" id="Inicio">
                        <img src="/assets/Inicio.png">
                        <span>INICIO</span>
                    </a>
                </li>
                    <li>
                        <a href="/HTML/perfil.html">
                            <img src="/assets/sesion.png">
                            <span>PERFIL</span>
                        </a>
                    </li>
                    <li>
                        <a href="/HTML/configuracion.html">
                            <img src="/assets/Configuracion.png">
                            <span>CONFIGURACION</span>
                        </a>
                    </li>
                    <li>
                        <a href="/HTML/reserva.php">
                            <img src="/assets/Reserva.png">
                            <span>RESERVA</span>
                        </a>
                    </li>
                    <li>
                        <a href="/PHP/facturacion.php">
                            <img src="/assets/Facturacion.png">
                            <span>FACTURACION</span>
                        </a>
                    </li>
                    <li>
                        <a href="/PHP/historialUsuario.php">
                            <img src="/assets/historialpk.png">
                            <span>HISTORIAL</span>
                        </a>
                    </li>
                    <li>
                        
                        <a>
                            <img src="/assets/Cerrar Sesion.png">
                            <span id="CerrarSesion">CERRAR SESION</span>
                        </a>
                    </li>
                    <li>
                        <a href="/HTML/ayuda.html">
                            <img src="/assets/Ayuda.png" >
                            <span>AYUDA</span>
                        </a>
                    </li>
            </nav>
        </div>
    
        <div></div>
                
        </div>
    
<main>

<?php
session_start();
include("conexion.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['correo'])) {
    echo "<h5>Debes iniciar sesión para ver tus reservas.</h5>";
    exit;
}

// Obtener el ID del usuario que está en sesión
$documento = $_SESSION['documento'];

// Obtener las reservas del usuario logueado
$sql = "SELECT * FROM reservas WHERE documentoUsuario = ? ORDER BY fecha DESC, hora_entrada DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $documento);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si hay reservas
if ($result->num_rows == 0) {
    echo "<h5>No tienes reservas registradas.</h5>";
    exit;
}
?>

    <div class="factura-container">
        <h1>FACTURA</h1>
        <div class="factura-info">
            <div class="factura-header">
                <h2>Datos del Usuario</h2>
                <p><strong>Correo:</strong> <?php echo $_SESSION['correo']; ?></p>
                <p><strong>Documento:</strong> <?php echo $_SESSION['documento']; ?></p>
            </div>

            <div class="reservas">
                <img src="/assets/LogoParadiseBlack.png">
                <?php
                // Recorrer todas las reservas del usuario logueado y mostrar en la factura
                while ($row = $result->fetch_assoc()) {
                    // Calcular el total a pagar
                    $costoPorHora = 2500; // Costo por hora de estacionamiento (ajustar según la lógica de negocio)
                    $horaEntrada = strtotime($row['hora_entrada']);
                    $horaSalida = strtotime($row['hora_salida']);
                    $duracionHoras = ($horaSalida - $horaEntrada) / 3600; // Duración en horas
                    $total = $costoPorHora * $duracionHoras;
                ?>
                    <div class="reserva-item">
                        <p><strong>Numero de Reserva:</strong> <?php echo htmlspecialchars($row['id']); ?></p>
                        <p><strong>Lugar Estacionamiento:</strong> <?php echo htmlspecialchars($row['lugar_estacionamiento']); ?></p>
                        <p><strong>Fecha de Reserva:</strong> <?php echo htmlspecialchars($row['fecha']); ?></p>
                        <p><strong>Hora de Entrada:</strong> <?php echo htmlspecialchars($row['hora_entrada']); ?></p>
                        <p><strong>Hora de Salida:</strong> <?php echo htmlspecialchars($row['hora_salida']); ?></p>
                        <p><strong>Tipo de Vehículo:</strong> <?php echo htmlspecialchars($row['tipo_vehiculo']); ?></p>
                        <p><strong>Placa:</strong> <?php echo htmlspecialchars($row['placa']); ?></p>
                        <p><strong>Total a Pagar:</strong> $<?php echo number_format($total, 2); ?></p>
                        <hr>
                    </div>
                <?php
                }
                ?>
            </div>

            <button onclick="window.print();">Imprimir Factura</button>
        </div>
    </div>
</main>

    <!-- Modal de confirmación de Cerrar Sesión -->
    <div id="miModalCerrarSesion" style="display: none;" class="cerrar">
    <div class="modalContent">
        <p id="mensajeModalCerrarSesion">¿Seguro que quieres cerrar sesión?</p>
        <button id="cerrarModalCerrarSesion">Aceptar</button>
        <button id="cancelarModalCerrarSesion">Cancelar</button>
    </div>
</div>

<script src="/JavaScript/scriptIndex.js"></script>
<script src="/JavaScript/script.js"></script>
</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>



