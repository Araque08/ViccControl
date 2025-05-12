function mostrarUsuarios() {
    fetch("usuarios_roles.php")
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoUsuarios").innerHTML = html;
        document.getElementById("usuariosModal").style.display = "block";
    });
}

function mostrarGestionRoles() {
    fetch("roles.php")
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoRoles").innerHTML = html;
        document.getElementById("rolesModal").style.display = "block";
    });
}

function mostrarEstadisticas() {
    fetch("")
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoEstadistica").innerHTML = html;
        document.getElementById("estadisticaModal").style.display = "block";
    });
}
function mostrarRestaurante() {
    fetch("crear_restaurante.php")
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoRestaurante").innerHTML = html;
        document.getElementById("restauranteModal").style.display = "block";
    });
}

function mostrarEditarRestaurante(id) {
    fetch('editar_restaurante.php?id=' + id)
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoEditarRestaurante").innerHTML = html;
        document.getElementById("editarRestauranteModal").style.display = "block";
    });
}

function mostrarModulos(id) {
    fetch('modulos_asignados.php?id=' + id)
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoVerModulos").innerHTML = html;
        document.getElementById("verModulosModal").style.display = "block";
    });
}

function mostrarRestaurante() {
    fetch("crear_restaurante.php")
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoRestaurante").innerHTML = html;
        document.getElementById("restauranteModal").style.display = "block";
    });
}

function cerrar(modal, event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
    
}

document.querySelector(".cerrar-modal").onclick = () => {
document.getElementById("usuariosModal").style.display = "none";
document.getElementById("rolesModal").style.display = "none";
document.getElementById("estadisticaModal").style.display = "none";
document.getElementById("restauranteModal").style.display = "none";
document.getElementById("editarRestauranteModal").style.display = "none";
document.getElementById("verModulosModal").style.display = "none";

};

// Cierra si se hace clic fuera del contenido
window.onclick = function(event) {
    let modal = document.getElementById("usuariosModal");
    modal.addEventListener('click', cerrar(modal, event));
    let modal2 = document.getElementById("rolesModal");
    modal.addEventListener('click', cerrar(modal2, event));
    let modal3 = document.getElementById("estadisticaModal");
    modal.addEventListener('click', cerrar(modal3, event));
    let modal4 = document.getElementById("restauranteModal");
    modal.addEventListener('click', cerrar(modal4, event));
    let modal5 = document.getElementById("editarRestauranteModal");
    modal.addEventListener('click', cerrar(modal5, event));
    let modal6 = document.getElementById("verModulosModal");
    modal.addEventListener('click', cerrar(modal6, event));
}


