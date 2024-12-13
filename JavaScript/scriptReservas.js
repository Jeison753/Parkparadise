const adminEmail = "admin@gmail.com";
const adminPassword = "admin123";

let isAdmin = false;

function toggleAdminRole() {
    isAdmin = !isAdmin;
    const button = document.getElementById('adminToggle');
    const icon = document.getElementById('roleIcon');

    if (isAdmin) {
        icon.src = "/Assets/administrador.png";
        alert("Modo Administrador activado.");
    } else {
        icon.src = "/Assets/sesion.png";
        alert("Modo Usuario activado.");
    }
}

document.getElementById('loginForm').addEventListener('submit', function (event) {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    if (isAdmin) {
        if (email !== adminEmail || password !== adminPassword) {
            event.preventDefault();
            alert("Solo las credenciales de administrador son válidas en este modo.");
        }
    }
});


