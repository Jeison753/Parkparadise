document.addEventListener('DOMContentLoaded', () => {
    fetch('/PHP/perfil.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.rta === 'OK') {
            console.log(data);
            document.getElementById('nombre').value = data.user.nombre;
            document.getElementById('correo').value = data.user.correo;
            document.getElementById('documento').value = data.user.documento;
            document.getElementById('telefono').value = data.user.telefono;
            document.getElementById('rol').value = data.user.rol;
        } else {
            console.error(data.message);
            alert('Error al cargar el perfil: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error);
    });
});


document.getElementById("formPerfil").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevenir el envío predeterminado del formulario

    const datos = {
        nombre: document.getElementById("nombre").value.trim(),
        correo: document.getElementById("correo").value.trim(),
        documento: document.getElementById("documento").value.trim(),
        telefono: document.getElementById("telefono").value.trim(),
        contrasena: document.getElementById("contrasena").value.trim(),
    };

    fetch("/PHP/configuracion.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(datos), // Convertir los datos a JSON
    })
    .then((response) => response.json())
    .then((data) => {
        // Mostrar el mensaje en el modal
        const mensajeModal = document.getElementById("mensajeModal");

        if (data.success) {
            mensajeModal.textContent = "El cambio del nombre se verá reflejado en su próximo inicio de sesión.";
        } else {
            mensajeModal.textContent = `Error: ${data.message}`;
        }

        // Mostrar el modal
        document.getElementById("miModal").style.display = "flex";

        // Al hacer clic en "Aceptar", cerrar el modal y redirigir solo si fue exitoso
        document.getElementById("cerrarModal").addEventListener("click", () => {
            document.getElementById("miModal").style.display = "none"; // Cerrar el modal

            // Redirigir solo si la operación fue exitosa
            if (data.success) {
                window.location.href = "/HTML/perfil.html"; // Redirigir al perfil
            }
        });
    })
    .catch((error) => {
        console.error("Error:", error);
        document.getElementById("mensajeModal").textContent = "Hubo un error al procesar la solicitud.";
        document.getElementById("miModal").style.display = "flex";

        // Al hacer clic en "Aceptar", solo cerrar el modal
        document.getElementById("cerrarModal").addEventListener("click", () => {
            document.getElementById("miModal").style.display = "none"; // Cerrar el modal
        });
    });
});

// Cerrar el modal cuando se presione "Aceptar" sin redirección en caso de error
document.getElementById("cerrarModal").addEventListener("click", () => {
    document.getElementById("miModal").style.display = "none";
});

