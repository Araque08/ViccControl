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
    document.getElementById("nuevaCategoriaModal").style.display = "none";
};

// Cierra si se hace clic fuera del contenido
window.onclick = function(event) {
    let modal = document.getElementById("nuevaCategoriaModal");
    modal.addEventListener('click', cerrar(modal, event));
}