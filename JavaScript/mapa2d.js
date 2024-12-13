document.addEventListener('DOMContentLoaded', () => {
    const parkingSpaces = document.querySelectorAll('.parking-space, .bike-parking');
    const tipoVehiculoSelect = document.getElementById('tipoVehiculo');

    // Función para resetear los espacios de estacionamiento
    const resetSpaces = () => {
        parkingSpaces.forEach(space => {
            space.classList.remove('selected', 'disabled');
            space.disabled = true; // Bloquea todos los espacios al inicio
            if (space.classList.contains('parking-space')) {
                space.style.backgroundColor = '#34c759'; // Verde para autos
            } else if (space.classList.contains('bike-parking')) {
                space.style.backgroundColor = '#0a84ff'; // Azul para motos
            }
            space.style.color = '';
        });
    };

    // Función para bloquear espacios según el tipo de vehículo seleccionado
    const bloquearEspaciosPorVehiculo = (tipoVehiculo) => {
        parkingSpaces.forEach(space => {
            if (tipoVehiculo === 'Selecciona tu tipo de vehiculo') {
                space.disabled = true; // Bloquea todos los espacios si no se selecciona un tipo
                space.style.backgroundColor = 'black'; // Restablece colores
                space.style.color = 'white';
            } else if (tipoVehiculo === 'carro') {
                if (space.classList.contains('bike-parking')) {
                    space.disabled = true;
                    space.style.backgroundColor = 'black'; // Bloquea espacios para motos
                    space.style.color = 'white';
                } else {
                    space.disabled = false;
                    space.style.backgroundColor = '#34c759'; // Verde para carros
                }
            } else if (tipoVehiculo === 'moto') {
                if (space.classList.contains('parking-space')) {
                    space.disabled = true;
                    space.style.backgroundColor = 'black'; // Bloquea espacios para carros
                    space.style.color = 'white';
                } else {
                    space.disabled = false;
                    space.style.backgroundColor = '#0a84ff'; // Azul para motos
                }
            }
        });
    };

    // Evento para cambiar el tipo de vehículo
    tipoVehiculoSelect.addEventListener('change', function () {
        const tipoVehiculo = this.value;
        resetSpaces(); // Restablece todos los espacios antes de aplicar restricciones
        bloquearEspaciosPorVehiculo(tipoVehiculo);
    });

    // Inicializar espacios bloqueados al cargar la página
    resetSpaces();

    // Seleccionar un espacio de estacionamiento
    parkingSpaces.forEach(space => {
        space.addEventListener('click', () => {
            if (space.disabled) {
                alert('Este espacio no está disponible para el tipo de vehículo seleccionado.');
                return;
            }

            // Restablecer el color de otros espacios seleccionados
            parkingSpaces.forEach(s => {
                if (s.classList.contains('parking-space')) {
                    s.style.backgroundColor = '#34c759'; // Verde para autos
                } else if (s.classList.contains('bike-parking')) {
                    s.style.backgroundColor = '#0a84ff'; // Azul para motos
                }
                s.style.color = '';
                s.classList.remove('selected');
            });

            // Marcar el espacio actual como seleccionado
            space.classList.add('selected');
            space.style.backgroundColor = 'red'; // Color rojo para indicar que está seleccionado
            space.style.color = 'white';

            const lugarEstacionamiento = document.getElementById('lugarEstacionamiento');
            if (lugarEstacionamiento) {
                lugarEstacionamiento.value = space.textContent.trim();
            }
        });
    });

    document.getElementById('formReserva').addEventListener('submit', async function (event) {
        event.preventDefault(); // Evitar el envío por defecto para manejarlo con JavaScript
    
        const lugarEstacionamiento = document.getElementById('lugarEstacionamiento').value;
        const placa = document.getElementById('placa').value;
        const tipoVehiculo = document.getElementById('tipoVehiculo').value;
    
        if (!lugarEstacionamiento || !placa || tipoVehiculo === 'Selecciona tu tipo de vehiculo') {
            alert('Por favor, completa todos los campos antes de enviar el formulario.');
            return;
        }
    
    });
});
