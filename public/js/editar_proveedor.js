function mostrarModal(id) {
    fetch('editar_proveedor.php?id=' + id)
        .then(res => res.text())
        .then(html => {
        document.getElementById("contenidoModal").innerHTML = html;
        document.getElementById("nuevoModal").style.display = "block";
    });
}


function cerrar(modal, event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
    
}

document.querySelector(".cerrar-modal").onclick = () => {
    document.getElementById("nuevoModal").style.display = "none";
};

// Cierra si se hace clic fuera del contenido
window.onclick = function(event) {
    let modal = document.getElementById("nuevoModal");
    modal.addEventListener('click', cerrar(modal, event));
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


