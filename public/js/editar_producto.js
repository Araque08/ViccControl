
document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const idProducto = urlParams.get("id");

        if (idProducto) {
            fetch('controller/Modulos/ventas/obtener_producto.php?id=${idProducto}')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('nombre').value = data.nombre_producto;
                        document.getElementById('precio').value = data.Precio_venta;

                        if (data.imagen_producto) {
                            const imagenPreview = document.createElement("p");
                            imagenPreview.innerHTML = '<img src="/../../../public/img/' + data.imagen_producto + '" width="200">';
                            const fileInput = document.getElementById("imagen_producto");
                            fileInput.parentNode.insertBefore(imagenPreview, fileInput);
                        }
                    }
                })
                .catch(error => console.error("Error al obtener el producto:", error));
        }
});
