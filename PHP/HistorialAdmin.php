<?php
session_start();

include("conexion.php");

// Parámetros de paginación
$limitePorPagina = 10; // Número máximo de registros por página
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $limitePorPagina;

// Lógica para mostrar todos los usuarios y reservas con paginación
$reservasResult = null;
$usuariosResult = null;

if (isset($_GET['mostrar_todos'])) {
    // Total de registros de usuarios y reservas
    $totalReservas = $conexion->query("SELECT COUNT(*) AS total FROM reservas")->fetch_assoc()['total'];
    $totalUsuarios = $conexion->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];

    // Consultar registros con límites y offset
    $reservasQuery = "SELECT * FROM reservas LIMIT $limitePorPagina OFFSET $offset";
    $usuariosQuery = "SELECT * FROM usuarios LIMIT $limitePorPagina OFFSET $offset";

    $reservasResult = $conexion->query($reservasQuery);
    $usuariosResult = $conexion->query($usuariosQuery);

    // Calcular el número total de páginas
    $totalPaginasReservas = ceil($totalReservas / $limitePorPagina);
    $totalPaginasUsuarios = ceil($totalUsuarios / $limitePorPagina);
}

// Lógica de búsqueda de usuarios
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $usuarioQuery = "SELECT * FROM usuarios WHERE documento = ? OR correo = ?";
    $stmtUsuario = $conexion->prepare($usuarioQuery);
    $stmtUsuario->bind_param('is', $search, $search);
    $stmtUsuario->execute();
    $usuarioResult = $stmtUsuario->get_result();
    $usuario = $usuarioResult->num_rows > 0 ? $usuarioResult->fetch_assoc() : null;

    if ($usuario) {
        $usuarioId = $usuario['documento'];

        // Total de reservas del usuario
        $totalReservasUsuario = $conexion->query("SELECT COUNT(*) AS total FROM reservas WHERE documentoUsuario = '$usuarioId'")->fetch_assoc()['total'];

        // Consultar reservas del usuario con paginación
        $reservasUsuarioQuery = "SELECT * FROM reservas WHERE documentoUsuario = ? LIMIT $limitePorPagina OFFSET $offset";
        $stmtReservas = $conexion->prepare($reservasUsuarioQuery);
        $stmtReservas->bind_param('s', $usuarioId);
        $stmtReservas->execute();
        $reservasUsuarioResult = $stmtReservas->get_result();

        // Calcular el número total de páginas para reservas del usuario
        $totalPaginasReservasUsuario = ceil($totalReservasUsuario / $limitePorPagina);
    } else {
        $reservasUsuarioResult = null;
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Admin - PARKPARADISE</title>
    <link rel="stylesheet" href="/CSS/styleHistorial.css">
    <link rel="stylesheet" href="/CSS/stylePrincipal.css">
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

        <div>
            <nav class="navegacion">
                <li>
                    <a href="/HTML/indexAdmin.html" id="Inicio">
                        <img src="/assets/Inicio.png">
                        <span>INICIO</span>
                    </a>
                </li>

                <li>
                    <a href="/PHP/AdministrarUsuario.php">
                        <img src="/assets/gestioOperadores.png">
                        <span>ADMINISTRAR USUARIO</span>
                    </a>
                </li>
                
                    <li>
                        <a href="/PHP/HistorialAdmin.php">
                            <img src="/assets/gestionUsuarios.png">
                            <span>HISTORIAL</span>
                        </a>
                    </li>

                    <li>
                        <a href="/index.html">
                            <img src="/assets/Cerrar Sesion.png">
                            <span>CERRAR SESION</span>
                        </a>
                    </li>
            </nav>
        </div>
    
        <div></div>
                
        </div>
    
<main>
<section>
    <h1>Historial Administrador</h1>
    <form method="GET" action="HistorialAdmin.php" class="buscador-form">
        <label for="search" class="buscador-label">Buscar Usuario:</label>
        <input type="text" id="search" name="search" class="buscador-input" placeholder="Documento o Correo" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required>
        <button type="submit" class="buscar-btn">Buscar</button>
    </form>

    <!-- Ejemplo para Mostrar Reservas del Usuario -->
    <?php if (isset($reservasUsuarioResult) && $reservasUsuarioResult->num_rows > 0) { ?>
        <h3>Reservas del Usuario</h3>
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

        <!-- Botones de Paginación -->
        <div class="paginacion">
            <form method="GET" action="HistorialAdmin.php">
                <?php if ($paginaActual > 1) { ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                    <input type="hidden" name="pagina" value="<?php echo $paginaActual - 1; ?>">
                    <button type="submit" class="paginacion-btn">Anterior</button>
                <?php } ?>

                <?php if ($paginaActual < $totalPaginasReservasUsuario) { ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                    <input type="hidden" name="pagina" value="<?php echo $paginaActual + 1; ?>">
                    <button type="submit" class="paginacion-btn">Siguiente</button>
                <?php } ?>
            </form>
        </div>
    <?php } ?>
</section>



</main>
<script src="/JavaScript/script.js"></script>
</body>
</html>


