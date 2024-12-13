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
