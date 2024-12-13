function mostrarAlerta(mensaje, tipo) {
    // Crear un contenedor para la alerta
    const alerta = document.createElement('div');
    alerta.textContent = mensaje;

    // Aplicar la clase según el tipo de alerta
    if (tipo === 'error') {
        alerta.className = 'ErrorAlert';
    } else if (tipo === 'success') {
        alerta.className = 'SucessAlert';
    }

    // Agregar la alerta al cuerpo del documento
    document.body.appendChild(alerta);

    // Configurar para que desaparezca después de 3 segundos
    setTimeout(() => {
        alerta.style.opacity = '0'; // Aplicar la transición de opacidad
        setTimeout(() => alerta.remove(), 1000); // Eliminar del DOM después de la animación
    }, 3000);
}
