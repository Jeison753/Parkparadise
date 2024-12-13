<?php
session_start();
include("conexion.php");

// Mensaje para mostrar resultados de operaciones
$mensaje = "";

// Actualizar usuario y su rol
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $nuevoRol = $_POST['rol'];

    // Validar que el rol solo pueda ser "usuario" o "operador"
    if ($nuevoRol !== 'usuario' && $nuevoRol !== 'operador') {
        $mensaje = "Rol no permitido.";
    } else {
        $actualizarQuery = "UPDATE usuarios SET nombre = ?, correo = ?, telefono = ?, rol = ? WHERE documento = ?";
        $stmtActualizar = $conexion->prepare($actualizarQuery);
        $stmtActualizar->bind_param('ssssi', $nombre, $correo, $telefono, $nuevoRol, $documento);

        if ($stmtActualizar->execute()) {
            $mensaje = "Usuario actualizado exitosamente.";
        } else {
            $mensaje = "Error al actualizar el usuario.";
        }
    }
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $documentoEliminar = (int)$_POST['documento_eliminar'];
    $eliminarQuery = "DELETE FROM usuarios WHERE documento = ?";
    $stmtEliminar = $conexion->prepare($eliminarQuery);
    $stmtEliminar->bind_param('i', $documentoEliminar);

    if ($stmtEliminar->execute()) {
        $mensaje = "Usuario eliminado exitosamente.";
    } else {
        $mensaje = "Error al eliminar el usuario.";
    }
}

// Lógica de búsqueda de usuarios y operadores
$searchResult = null;
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search) {
    // Buscar tanto usuarios como operadores por documento o correo
    $searchQuery = "SELECT * FROM usuarios WHERE (documento LIKE ? OR correo LIKE ?)";
    $stmtSearch = $conexion->prepare($searchQuery);
    $searchParam = "%$search%";
    $stmtSearch->bind_param('ss', $searchParam, $searchParam);
    $stmtSearch->execute();
    $searchResult = $stmtSearch->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios - PARKPARADISE</title>
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
    <h1>Administrar Usuarios</h1>
    <?php if ($mensaje) { ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php } ?>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="AdministrarUsuario.php" class="buscador-form">
        <label for="search" class="buscador-label">Buscar</label>
        <input type="text" id="search" name="search" class="buscador-input" placeholder="Documento o Correo" value="<?php echo htmlspecialchars($search); ?>" required>
        <button type="submit" class="buscar-btn">Buscar</button>
    </form>

    <!-- Tabla para mostrar los resultados de búsqueda -->
    <?php if ($search && $searchResult && $searchResult->num_rows > 0) { ?>
        <h3>Resultado de la Búsqueda</h3>
        <table class="tablaDatos">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $searchResult->fetch_assoc()) { ?>
                    <tr>
                        <form method="POST" action="AdministrarUsuario.php" class="formulario-usuario">
                            <td>
                                <input type="hidden" name="documento" value="<?php echo $row['documento']; ?>">
                                <?php echo $row['documento']; ?>
                            </td>
                            <td><input type="text" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required></td>
                            <td><input type="email" name="correo" value="<?php echo htmlspecialchars($row['correo']); ?>" required></td>
                            <td><input type="text" name="telefono" value="<?php echo htmlspecialchars($row['telefono']); ?>" required></td>
                            <td>
                                <select name="rol">
                                    <option value="usuario" <?php echo $row['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="operador" <?php echo $row['rol'] === 'operador' ? 'selected' : ''; ?>>Operador</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="actualizar" class="editar-btn">Actualizar</button>
                                <form method="POST" action="AdministrarUsuario.php" style="display:inline;">
                                    <input type="hidden" name="documento_eliminar" value="<?php echo $row['documento']; ?>">
                                    <button type="submit" name="eliminar" class="eliminar-btn" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</button>
                                </form>
                            </td>
                        </form>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } elseif ($search) { ?>
        <p>No se encontró ningún usuario o operador con el documento o correo proporcionado.</p>
    <?php } ?>
</section>
</main>
</main>

<script src="/JavaScript/script.js"></script>
<script src="/JavaScript/alertas.js"></script>
</body>
</html>