// Obtener el nombre del usuario desde la sesión
fetch("../PHP/index.php")
    .then(response => response.json())
    .then(data => {
        if (data.nombre) {
            // Actualiza el contenido de las etiquetas con el ID "nombre"
            document.querySelectorAll("#nombreUsu").forEach(el => {
                el.textContent = data.nombre;
            });
        } else {
            console.log("Usuario no encontrado en la sesión");
        }
    })
    .catch(error => console.error("Error al obtener el usuario:", error));

    document.getElementById("CerrarSesion").addEventListener("click", function () {
        // Mostrar el modal de confirmación de cierre de sesión
        document.getElementById("miModalCerrarSesion").style.display = "flex";
    
        // Al hacer clic en "Aceptar" en el modal de cierre de sesión
        document.getElementById("cerrarModalCerrarSesion").addEventListener("click", function () {
            // Redirigir al archivo de cierre de sesión
            window.location.href = "../PHP/CerrarSesion.php";
        });
    
        // Al hacer clic en "Cancelar" en el modal de cierre de sesión
        document.getElementById("cancelarModalCerrarSesion").addEventListener("click", function () {
            // Cerrar el modal sin hacer nada
            document.getElementById("miModalCerrarSesion").style.display = "none";
        });
    });
    
