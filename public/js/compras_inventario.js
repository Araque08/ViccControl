// Abrir el modal para agregar nueva categoría
document.getElementById('nuevaCategoriaOption').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('categoriaModal').style.display = "block";
});

// Cerrar el modal
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('categoriaModal').style.display = "none";
});

// Cerrar el modal si se hace clic fuera del modal
window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('categoriaModal')) {
        document.getElementById('categoriaModal').style.display = "none";
    }
});

// Simulación de agregar nueva categoría
document.getElementById('newCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var newCategoria = event.target.new_categoria.value;
    var categoriaSelect = document.getElementById('categoria');
    var newOption = document.createElement('option');
    newOption.value = newCategoria;
    newOption.textContent = newCategoria;
    categoriaSelect.appendChild(newOption);
    categoriaSelect.value = newCategoria; // Establecer la nueva categoría seleccionada
    document.getElementById('categoriaModal').style.display = "none"; // Cerrar el modal
});