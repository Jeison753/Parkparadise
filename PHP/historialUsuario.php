<?php
session_start();

include("conexion.php");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['correo'])) {
    header("Location: /index.html");
    exit;
}

$correoUsuario = $_SESSION['correo'];

// Consultar datos del usuario actual por correo
$usuarioQuery = "SELECT * FROM usuarios WHERE correo = ?";
$stmtUsuario = $conexion->prepare($usuarioQuery);
$stmtUsuario->bind_param('s', $correoUsuario);
$stmtUsuario->execute();
$usuarioResult = $stmtUsuario->get_result();
$usuario = $usuarioResult->fetch_assoc();

if ($usuario) {
    $usuarioId = $usuario['documento'];

    // Paginación
    $registrosPorPagina = 10; // Número de registros por página
    $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $offset = ($paginaActual - 1) * $registrosPorPagina;

    // Consultar reservas del usuario actual con paginación
    $reservasUsuarioQuery = "SELECT * FROM reservas WHERE documentoUsuario = ? LIMIT ? OFFSET ?";
    $stmtReservas = $conexion->prepare($reservasUsuarioQuery);
    $stmtReservas->bind_param('sii', $usuarioId, $registrosPorPagina, $offset);
    $stmtReservas->execute();
    $reservasUsuarioResult = $stmtReservas->get_result();

    // Contar el total de reservas para calcular el número de páginas
    $totalReservasQuery = "SELECT COUNT(*) AS total FROM reservas WHERE documentoUsuario = ?";
    $stmtTotalReservas = $conexion->prepare($totalReservasQuery);
    $stmtTotalReservas->bind_param('s', $usuarioId);
    $stmtTotalReservas->execute();
    $totalReservasResult = $stmtTotalReservas->get_result();
    $totalReservas = $totalReservasResult->fetch_assoc()['total'];

    $totalPaginas = ceil($totalReservas / $registrosPorPagina); // Total de páginas
} else {
    $reservasUsuarioResult = null;
    $totalPaginas = 0;
    $paginaActual = 1;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - PARKPARADISE</title>
    <link rel="stylesheet" href="/CSS/stylePrincipal.css">
    <link rel="stylesheet" href="/CSS/styleHistorial.css">
    <link rel="icon" href="/assets/LogoParadise.png" type="image/png">
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
<section>
    <h1>Mis Reservas</h1>
    <?php if ($usuario) { ?>
        <table class="tablaDatos">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $usuario['documento']; ?></td>
                    <td><?php echo $usuario['nombre']; ?></td>
                    <td><?php echo $usuario['correo']; ?></td>
                    <td><?php echo $usuario['rol']; ?></td>
                    <td><?php echo $usuario['telefono']; ?></td>
                </tr>
            </tbody>
        </table>

        <h3>Reservas Realizadas</h3>
        <?php if ($reservasUsuarioResult && $reservasUsuarioResult->num_rows > 0) { ?>
            <table class="tablaDatos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehículo</th>
                        <th>Placa</th>
                        <th>Fecha</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Estacionamiento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reserva = $reservasUsuarioResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $reserva['documentoUsuario']; ?></td>
                            <td><?php echo $reserva['tipo_vehiculo']; ?></td>
                            <td><?php echo $reserva['placa']; ?></td>
                            <td><?php echo $reserva['fecha']; ?></td>
                            <td><?php echo $reserva['hora_entrada']; ?></td>
                            <td><?php echo $reserva['hora_salida']; ?></td>
                            <td><?php echo $reserva['lugar_estacionamiento']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Controles de Paginación con Botones -->
            <div class="paginacion">
                <form method="GET" action="historialUsuario.php">
                    <?php if ($paginaActual > 1) { ?>
                        <button type="submit" name="pagina" value="<?php echo $paginaActual - 1; ?>" class="btn-paginacion">Anterior</button>
                    <?php } ?>

                    <?php if ($paginaActual < $totalPaginas) { ?>
                        <button type="submit" name="pagina" value="<?php echo $paginaActual + 1; ?>" class="btn-paginacion">Siguiente</button>
                    <?php } ?>
                </form>
            </div>
        <?php } else { ?>
            <p>No tienes reservas registradas.</p>
        <?php } ?>
    <?php } else { ?>
        <p>No se encontraron datos del usuario.</p>
    <?php } ?>
</section>

</main>
</body>
</html>
</main>

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
