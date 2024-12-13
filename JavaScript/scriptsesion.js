// Credenciales específicas para el administrador
const adminEmail = "admin@gmail.com";
const adminPassword = "admin123";


function mostrarAlerta(mensaje) {
    const alertOverlay = document.getElementById("customAlert");
    const alertMessage = document.getElementById("alertMessage");

    alertMessage.textContent = mensaje; 
    alertOverlay.style.display = "flex";


    setTimeout(() => {
        alertOverlay.style.display = "none";
    }, 3000); // 1000 milisegundos = 1 segundo
}


// Validación de credenciales
function validarInicio() {
    let email = document.getElementById("correo");
    let emailVal = email.value.trim();

    let password = document.getElementById("contrasena");
    let passwordVal = password.value.trim();

    let valEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let validar = true;

    // Validar correo
    let mensajeCorreo = document.getElementById("errorCorreo");
    if (!valEmail.test(emailVal)) {
        mensajeCorreo.innerHTML = "Por favor, ingresa un correo válido.";
        mensajeCorreo.style.color = "red";
        mostrarAlerta("Por favor, ingresa un correo válido.");
        validar = false;
    } else {
        mensajeCorreo.innerHTML = "";
    }

    // Validar contraseña
    let mensajePass = document.getElementById("errorContrasena");
    if (passwordVal === "") {
        mensajePass.innerHTML = "Por favor, escribe una contraseña.";
        mensajePass.style.color = "red";
        mostrarAlerta("Por favor, escribe una contraseña.");
        validar = false;
    } else {
        mensajePass.innerHTML = "";
    }

    // Si todo es válido, devolver los datos
    if (validar) {
        return {
            Correo: emailVal,
            Contrasena: passwordVal
        };
    } else {
        return null;
    }
}

// Enviar datos al servidor
function iniciarPost(dicc_datos) {
    let ladata = new FormData();
    ladata.append("CORREO", dicc_datos.Correo);
    ladata.append("CONTRASENA", dicc_datos.Contrasena);

    // Verificar si las credenciales son de administrador
    if (dicc_datos.Correo === adminEmail && dicc_datos.Contrasena === adminPassword) {
        console.log("Inicio de sesión como administrador exitoso.");
        window.location.href = "../HTML/indexAdmin.html"; // Redirige al panel de administrador
    } else {
        fetch("../PHP/sesion.php", {
            method: 'POST',
            body: ladata,
        })
        .then(response => response.json())
        .then(function(datos) {
            if (datos.rta === 'OK') {
                console.log("Inicio de sesión exitoso");
                window.location.href = datos.redirect;
            }else {
                mostrarAlerta("Correo o contraseña incorrectos.");
            }
            
        })
        .catch(function(error) {
            console.error("Error en la petición:", error);
            alert("Error al comunicarse con el servidor. Intenta nuevamente.");
        });
    }
}

// Evento de clic en el botón de inicio de sesión
document.getElementById("btnIniciar").addEventListener("click", function(e) {
    e.preventDefault();
    const dicc_datos = validarInicio();

    if (dicc_datos) {
        iniciarPost(dicc_datos);
    } else {
        console.log("Errores de validación");
    }
});

