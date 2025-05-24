function mostrarCategoria() {
    fetch("añadir_categoria_productos.php")
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoCategoria").innerHTML = html;
        document.getElementById("categoriaModal").style.display = "block";
    });
}

function mostrarEdicion(id) {
    fetch('editar_categoria.php?id=' + id)
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoEditar").innerHTML = html;
        document.getElementById("editarModal").style.display = "block";
    });
}

function cerrar(modal, event) { 
    if (event.target == modal) {
        modal.style.display = "none";
    }
    
}

// Función para mostrar y ocultar el menú de opciones
function toggleMenu(categoryId) {
    // Obtener el elemento del menú de opciones correspondiente
    const menu = document.getElementById("menu-" + categoryId);

    // Toggle (cambiar) la visibilidad del menú
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}


document.querySelector(".cerrar-modal").onclick = () => {
document.getElementById("categoriaModal").style.display = "none";
document.getElementById("editarModal").style.display = "none";
};

// Cierra si se hace clic fuera del contenido
window.onclick = function(event) {
    let modal = document.getElementById("categoriaModal");
    let modal1 = document.getElementById("editarModal");
    modal.addEventListener('click', cerrar(modal, event));
    modal1.addEventListener('click', cerrar(modal1, event));
}

