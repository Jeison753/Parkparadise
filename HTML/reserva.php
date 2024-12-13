<?php
session_start();
include("../PHP/conexion.php");

// Verificar si el correo del usuario está en la sesión
if (!isset($_SESSION['correo'])) {
    echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión para realizar una reserva.']);
    exit;
}

$correo = $_SESSION['correo'];

// Obtener el ID del usuario basado en el correo
$sqlUsuario = "SELECT documento FROM usuarios WHERE correo = ?";
$stmtUsuario = $conexion->prepare($sqlUsuario);
$stmtUsuario->bind_param('s', $correo);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();

if ($resultUsuario->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
    $stmtUsuario->close();
    $conexion->close();
    exit;
}

$usuario = $resultUsuario->fetch_assoc();
$documento = $usuario['documento'];
$stmtUsuario->close();

// Variables recibidas del formulario
$lugarEstacionamiento = $_POST['lugarEstacionamiento'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$horaEntrada = $_POST['horaEntrada'] ?? '';
$horaSalida = $_POST['horaSalida'] ?? '';
$tipoVehiculo = $_POST['tipoVehiculo'] ?? '';
$placa = $_POST['placa'] ?? '';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validación de campos
    if (empty($lugarEstacionamiento) || empty($fecha) || empty($horaEntrada) || empty($horaSalida) || empty($tipoVehiculo) || empty($placa)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos, incluyendo el tipo de vehículo y la placa.']);
        exit;
    }

    // Validar formato de la placa
    if (!preg_match('/^[A-Z0-9]{3,6}$/', $placa)) {
        echo json_encode(['success' => false, 'message' => 'La placa ingresada no es válida.']);
        exit;
    }

    // Validar traslapes de horarios en el mismo lugar
    $query = "SELECT COUNT(*) as conteo FROM reservas 
              WHERE lugar_estacionamiento = ? 
              AND (
                  (fecha = ? AND hora_entrada <= ? AND hora_salida > ?) OR 
                  (fecha = ? AND hora_entrada < ? AND hora_salida >= ?)
              )";

    $stmt = $conexion->prepare($query);
    $stmt->bind_param('sssssss', $lugarEstacionamiento, $fecha, $horaSalida, $horaEntrada, $fecha, $horaSalida, $horaEntrada);
    $stmt->execute();
    $resultTraslape = $stmt->get_result()->fetch_assoc();

    if ($resultTraslape['conteo'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya existe una reserva que traslapa con este horario y espacio.']);
        $stmt->close();
        $conexion->close();
        exit;
    }

    $stmt->close();

    // Insertar nueva reserva
    $sql = "INSERT INTO reservas (lugar_estacionamiento, fecha, hora_entrada, hora_salida, tipo_vehiculo, placa, documentoUsuario) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('sssssss', $lugarEstacionamiento, $fecha, $horaEntrada, $horaSalida, $tipoVehiculo, $placa, $documento);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reserva realizada con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Hubo un error al realizar la reserva.']);
    }

    $stmt->close();
}

$conexion->close();
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva tu estacionamiento</title>
    <link rel="stylesheet" href="/Css/styleReserva.css">
    <link rel="stylesheet" href="/Css/mapa.css">
    <link rel="stylesheet" href="/Css/alertas.css">
    <link rel="stylesheet" href="/CSS/stylePrincipal.css">
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
        <section class="formulario-container">
            <h2>Reserva tu estacionamiento</h2>
            <form id="formReserva" action="#" method="POST">
                <label for="tipoVehiculo">Tipo de Vehículo</label>
                <select id="tipoVehiculo" name="tipoVehiculo" required>
                    <option value="Selecciona tu tipo de vehiculo" class="Listado"> Selecciona tu tipo de vehiculo </option>
                    <option value="carro" class="Listado" >Carro</option>
                    <option value="moto" class="Listado" >Moto</option>
                </select>

                <label for="placa">Placa</label>
                <input type="text" id="placa" name="placa" required placeholder="Ej: ABC123 o ABC12D" title="Formato: ABC123 para carro, ABC12D para moto.">

                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" required>

                <label for="horaEntrada">Hora de Entrada</label>
                <input type="time" id="horaEntrada" name="horaEntrada" required>

                <label for="horaSalida">Hora de Salida</label>
                <input type="time" id="horaSalida" name="horaSalida" required>

                <label for="lugarEstacionamiento">Lugar de Estacionamiento</label>
                <input type="text" id="lugarEstacionamiento" name="lugarEstacionamiento" placeholder="C1 o M1" readonly required>
                <button type="submit" id="btnReservar">Reservar</button>
            </form>
        </section>

        <section class="mapa-container">
    <div id="parking-lot" aria-label="Mapa de estacionamiento interactivo">
        <!-- Fila 1 -->
        <div class="empty-space"></div>
        <button class="parking-space" aria-label="Espacio C1" data-lugar="C1">C1</button>
        <button class="parking-space" aria-label="Espacio C2" data-lugar="C2">C2</button>
        <button class="parking-space" aria-label="Espacio C3" data-lugar="C3">C3</button>
        <button class="parking-space" aria-label="Espacio C4" data-lugar="C4">C4</button>
        <button class="parking-space" aria-label="Espacio C5" data-lugar="C5">C5</button>
        <button class="parking-space" aria-label="Espacio C6" data-lugar="C6">C6</button>
        <button class="parking-space" aria-label="Espacio C7" data-lugar="C7">C7</button>
        <div class="empty-space"></div>

        <!-- Fila 2 -->
        <button class="parking-space" aria-label="Espacio C8" data-lugar="C8">C8</button>
        <div class="empty-space">←</div>
        <div class="empty-space">←</div>
        <div class="empty-space">←</div>
        <div class="empty-space">←</div>
        <div class="empty-space">←</div>
        <div class="empty-space">←</div>
        <div class="empty-space">←</div>

        <!-- Fila 3 -->
        <button class="parking-space" aria-label="Espacio C9" data-lugar="C9">C9</button>
        <button class="parking-space" aria-label="Espacio C10" data-lugar="C10">C10</button>
        <div class="empty-space">↓</div>
        <button class="bike-parking" aria-label="Espacio M1" data-lugar="M1">M1</button>

        <!-- Fila 4 -->
        <button class="bike-parking" aria-label="Espacio M2" data-lugar="M2">M2</button>
        <div class="empty-space">↑</div>
        <button class="bike-parking" aria-label="Espacio M3" data-lugar="M3">M3</button>
        <button class="bike-parking" aria-label="Espacio M4" data-lugar="M4">M4</button>
        <div class="empty-space">↑</div>
        <button class="parking-space" aria-label="Espacio C11" data-lugar="C11">C11</button>

        <!-- Fila 5 -->
        <button class="parking-space" aria-label="Espacio C12" data-lugar="C12">C12</button>
        <div class="empty-space">↓</div>
        <button class="bike-parking" aria-label="Espacio M5" data-lugar="M5">M5</button>
        <button class="bike-parking" aria-label="Espacio M6" data-lugar="M6">M6</button>
        <div class="empty-space">↑</div>

        <!-- Fila 6 -->
        <button class="bike-parking" aria-label="Espacio M7" data-lugar="M7">M7</button>
        <button class="bike-parking" aria-label="Espacio M8" data-lugar="M8">M8</button>
        <div class="empty-space">↑</div>
        <button class="parking-space" aria-label="Espacio C13" data-lugar="C13">C13</button>

        <!-- Fila 7 -->
        <button class="parking-space" aria-label="Espacio C14" data-lugar="C14">C14</button>
        <div class="empty-space">↓</div>
        <button class="bike-parking" aria-label="Espacio M9" data-lugar="M9">M9</button>

        <!-- Fila 8 -->
        <button class="bike-parking" aria-label="Espacio M10" data-lugar="M10">M10</button>
        <div class="empty-space">↑</div>
        <button class="bike-parking" aria-label="Espacio M11" data-lugar="M11">M11</button>
        <button class="bike-parking" aria-label="Espacio M12" data-lugar="M12">M12</button>
        <div class="empty-space">↑</div>
        <button class="parking-space" aria-label="Espacio C15" data-lugar="C15">C15</button>
        <button class="parking-space" aria-label="Espacio C16" data-lugar="C16">C16</button>
        <div class="empty-space">↓</div>
        <button class="bike-parking" aria-label="Espacio M13" data-lugar="M13">M13</button>
        <button class="bike-parking" aria-label="Espacio M14" data-lugar="M14">M14</button>
        <div class="empty-space">↑</div>
        <button class="bike-parking" aria-label="Espacio M15" data-lugar="M15">M15</button>
        <button class="bike-parking" aria-label="Espacio M16" data-lugar="M16">M16</button>
        <div class="empty-space">↑</div>
        <button class="parking-space" aria-label="Espacio C17" data-lugar="C17">C17</button>

        <!-- Fila 9 -->
        <div class="empty-space">→</div>
        <div class="empty-space">→</div>
        <div class="empty-space">→</div>
        <div class="empty-space">→</div>
        <div class="empty-space">→</div>
        <div class="empty-space">→</div>
        <div class="empty-space">→</div>
        <div class="empty-space">→</div>
        <div class="empty-space">→</div>
    </div>


        <!-- Modal de confirmación de Cerrar Sesión -->
<div id="miModalCerrarSesion" style="display: none;" class="cerrar">
    <div class="modalContent">
        <p id="mensajeModalCerrarSesion">¿Seguro que quieres cerrar sesión?</p>
        <button id="cerrarModalCerrarSesion">Aceptar</button>
        <button id="cancelarModalCerrarSesion">Cancelar</button>
    </div>
</div>

</section>


<script>
// Función para asignar el lugar de estacionamiento al campo del formulario
document.querySelectorAll('.parking-space, .bike-parking').forEach(button => {
    button.addEventListener('click', function() {
        if (!this.disabled) {
            const lugar = this.getAttribute('data-lugar');  // Obtener el lugar
            document.getElementById('lugarEstacionamiento').value = lugar;  // Asignar al campo de formulario
        }
    });
});


</script>

</Main>

<script src="/JavaScript/scriptIndex.js"></script>
<script src="/JavaScript/mapa2d.js"></script>
<script src="/JavaScript/script.js"></script>
<script src="/JavaScript/reserva.js"></script>
<script src="/JavaScript/alertas.js"></script>

</body>
</html>