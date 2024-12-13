// Validaciones
function validarFormularioReserva() {
    const tipoVehiculo = document.getElementById("tipoVehiculo").value;
    const placa = document.getElementById("placa").value.trim();
    const fecha = document.getElementById("fecha").value;
    const horaEntrada = document.getElementById("horaEntrada").value;
    const horaSalida = document.getElementById("horaSalida").value;
    const lugarEstacionamiento = document.getElementById("lugarEstacionamiento").value;

    // Validar tipo de vehículo
    if (!tipoVehiculo || tipoVehiculo === "Selecciona tu tipo de vehiculo") {
        mostrarAlerta("Por favor selecciona el tipo de vehículo.", "error");
        return false;
    }

    // Validar placa según el tipo de vehículo
    const placaCarro = /^[A-Z]{3}\d{3}$/; // Formato ABC123
    const placaMoto = /^[A-Z]{3}\d{2}[A-Z]$/; // Formato ABC12D

    if (tipoVehiculo === "carro") {
        if (!placaCarro.test(placa)) {
            mostrarAlerta("La placa del carro debe tener el formato ABC123.", "error");
            return false;
        }
    } else if (tipoVehiculo === "moto") {
        if (!placaMoto.test(placa)) {
            mostrarAlerta("La placa de la moto debe tener el formato ABC12D.", "error");
            return false;
        }
    }

    // Validar que las placas coincidan con el tipo de vehículo
    if (tipoVehiculo === "carro" && placaMoto.test(placa)) {
        mostrarAlerta("El formato de placa ingresado es para motos. Selecciona el tipo de vehículo correcto.", "error");
        return false;
    }
    if (tipoVehiculo === "moto" && placaCarro.test(placa)) {
        mostrarAlerta("El formato de placa ingresado es para carros. Selecciona el tipo de vehículo correcto.", "error");
        return false;
    }

    // Validar fecha
    if (!fecha) {
        mostrarAlerta("Por favor selecciona una fecha.", "error");
        return false;
    }

    // Validar horas
    if (!horaEntrada || !horaSalida) {
        mostrarAlerta("Por favor selecciona las horas de entrada y salida.", "error");
        return false;
    }
    if (horaEntrada >= horaSalida) {
        mostrarAlerta("La hora de salida debe ser mayor que la de entrada", "error");
        return false;
    }

    // Validar lugar de estacionamiento
    if (!lugarEstacionamiento) {
        mostrarAlerta("Por favor selecciona un lugar de estacionamiento.", "error");
        return false;
    }

    return true;
}



// Configurar mapa de estacionamiento
function configurarMapaEstacionamiento() {
    document.querySelectorAll(".parking-space, .bike-parking").forEach(button => {
        button.addEventListener("click", function () {
            if (!this.disabled) {
                const lugar = this.getAttribute("data-lugar");
                document.getElementById("lugarEstacionamiento").value = lugar;
            }
        });
    });
}


// Función para verificar disponibilidad antes de proceder con la reserva
function verificarDisponibilidad(dicc_datos, callback) {
    let ladata = new FormData();
    ladata.append("fechaInicio", dicc_datos.fechaInicio);
    ladata.append("fechaFin", dicc_datos.fechaFin);
    ladata.append("lugarEstacionamiento", dicc_datos.lugarEstacionamiento);

    fetch("/HTML/reserva.php", {
        method: "POST",
        body: ladata,
    })
    .then(response => response.json())
    .then(data => {
        callback(data.existe); // Devuelve true si ya existe la reserva
    })
    .catch(error => console.error("Error al verificar disponibilidad:", error));
}

// Manejo del botón "submit" (modificado para incluir verificación)
document.getElementById("btnReservar").addEventListener("click", function (e) {
    e.preventDefault(); // Prevenir el comportamiento predeterminado del botón

    if (validarFormularioReserva()) {
        const dicc_datos = {
            placa: document.getElementById("placa").value.trim(),
            lugarEstacionamiento: document.getElementById("lugarEstacionamiento").value,
            tipoVehiculo: document.getElementById("tipoVehiculo").value,
            fechaInicio: document.getElementById("fecha").value + "T" + document.getElementById("horaEntrada").value,
            fechaFin: document.getElementById("fecha").value + "T" + document.getElementById("horaSalida").value,
        };

        // Realiza la verificación de disponibilidad antes de proceder con la reserva
        verificarDisponibilidad(dicc_datos, (existe) => {
            if (existe) {
                mostrarAlerta("Ya existe una reserva en este horario y lugar. Por favor, selecciona otro.", "error");
            } else {
                const form = document.getElementById("formReserva");
                const formData = new FormData(form);

                // Realizar la petición al servidor para registrar la reserva
                fetch(form.action, {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes("error")) {
                        mostrarAlerta(data, "error");
                    } else {
                        mostrarAlerta("Reserva realizada con éxito.", "success");
                        setTimeout(() => window.location.reload(), 2000);
                    }
                })
                .catch(error => {
                    console.error("Error en la solicitud:", error);
                    mostrarAlerta("Ocurrió un error al procesar tu solicitud.", "error");
                });
            }
        });
    } else {
        console.log("Errores en la validación");
    }
});

// Inicializar el mapa y deshabilitar los espacios ocupados al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    const espaciosOcupados = window.espaciosOcupados || [];
    deshabilitarEspaciosOcupados(espaciosOcupados);
    configurarMapaEstacionamiento();
});
