function validarRegistro(){

    let nombre = document.getElementById("nombre");
    let nombreVal = nombre.value.trim();

    let email = document.getElementById("correoRe");
    let emailVal = email.value.trim();

    let password = document.getElementById("contrasenaRe");
    let passwordVal = password.value.trim();

    let telefono = document.getElementById("telefono");
    let telefonoVal = telefono.value.trim();

    let documento = document.getElementById("documento");
    let documentoVal = documento.value.trim();

    let valEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let validar = true;

    //validacion del correo
    let mensajeCorreo = document.getElementById("errorCorreoRe");
    if (!valEmail.test(emailVal)) {
        mensajeCorreo.innerHTML = "Por favor, escribe un correo válido.";
        mensajeCorreo.style.color = "red";
        validar = false;
    } else {
        mensajeCorreo.innerHTML = "";
    }

    //validacion del nombre
    let mensajeNombre = document.getElementById("errorNombre");
    if (nombreVal === "") {
        mensajeNombre.innerHTML = "Por favor, escribe tu nombre.";
        mensajeNombre.style.color = "red";
        validar = false;
    } else {
        mensajeNombre.innerHTML = "";
    }

    //validacion de la contraseña
    let mensajePass = document.getElementById("errorContrasenaRe");
    if (passwordVal === "") {
        mensajePass.innerHTML = "Por favor, escribe una contraseña.";
        mensajePass.style.color = "red";
        validar = false;
    } else {
        mensajePass.innerHTML = "";
    }

    //validacion de telefono
    let mensajeTelefono = document.getElementById("errorTelefono");
    if (telefonoVal === "") {
        mensajeTelefono.innerHTML = "Por favor, escribe un telefono.";
        mensajeTelefono.style.color = "red";
        validar = false;
    } else {
        mensajeTelefono.innerHTML = "";
    }

    //validacion del documento
    let mensajeDocumento = document.getElementById("errorDocumento");
    if (documentoVal === "") {
        mensajeDocumento.innerHTML = "Por favor, escribe un documento.";
        mensajeDocumento.style.color = "red";
        validar = false;
    } else {
        mensajeDocumento.innerHTML = "";
    }

    //si todo es correcto devolver los datos
    if (validar) {
        return {
            nombre: nombreVal,
            emailRe: emailVal,
            passwordRe: passwordVal,
            telefono: telefonoVal,
            documento: documentoVal
        };
    }else{
        return null;
    }

}

function registerPost(dicc_datos){
    let ladata = new FormData();
    ladata.append("NOMBRE", dicc_datos.nombre.trim());
    ladata.append("CORREORE", dicc_datos.emailRe.trim());
    ladata.append("CONTRASENARE", dicc_datos.passwordRe.trim());
    ladata.append("TELEFONO", dicc_datos.telefono.trim());
    ladata.append("DOCUMENTO", dicc_datos.documento.trim());

    fetch("../PHP/registro.php", {
        method: 'POST',
        body: ladata,
    })
   .then(response => response.text())
   .then(function(datos) {
    console.log("respuesta recibida", datos);
    console.log(dicc_datos);
   })
   .catch(function(error) {
        console.error("Error en la petición:", error);
        alert("Error al comunicarse con el servidor. Intenta nuevamente.");
    });
}

document.getElementById("btnRegistrar").addEventListener("click", function(e){
    e.preventDefault();
    const dicc_datos = validarRegistro();

    if (dicc_datos) {
        registerPost(dicc_datos);
    } else {
        console.log("errores en la validacion");
    }
 });

 document.getElementById("btnRegistrar").addEventListener("click", function () {
    // Mostrar el modal de confirmación de cierre de sesión
    document.getElementById("miModalRegistro").style.display = "flex";

    // Al hacer clic en "Aceptar" en el modal de cierre de sesión
    document.getElementById("cerrarModalRegistro").addEventListener("click", function () {
        // Redirigir al archivo de cierre de sesión
        window.location.href = "../index.html";
    });

});