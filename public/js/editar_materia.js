function mostrarModal(id) {
    fetch('editar_materia.php?id=' + id)
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoModal").innerHTML = html;
        document.getElementById("nuevoModal").style.display = "block";
    });
}

function mostrarNuevaCategoria(id) {
    fetch('categoria_materiaPrima.php?id=' + id)
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoNuevaCategoria").innerHTML = html;
        document.getElementById("nuevaCategoriaModal").style.display = "block";
    });
}


function cerrar(modal, event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
    
}

document.querySelector(".cerrar-modal").onclick = () => {
    document.getElementById("nuevoModal").style.display = "none";
    document.getElementById("nuevaCategoriaModal").style.display = "none";
};

// Cierra si se hace clic fuera del contenido
window.onclick = function(event) {
    let modal = document.getElementById("nuevoModal");
    let modal1 = document.getElementById("nuevaCategoriaModal");
    modal.addEventListener('click', cerrar(modal, event));
    modal.addEventListener('click', cerrar(modal1, event));
}

document.addEventListener("DOMContentLoaded", () => {
    const alert = document.querySelector('.alert-success');
    if (alert) {
    setTimeout(() => {
        alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 800); // remueve del DOM tras la transición
    }, 3000);
    }
});
