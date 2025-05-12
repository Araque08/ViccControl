// Escuchar el cambio en el select de compras
document.getElementById('compra').addEventListener('change', function() {
    const compraId = this.value; // Obtener el ID de la compra seleccionada

    if (compraId !== 'nada') {
        // Si se selecciona una compra, hacer la solicitud al backend
        fetch('../../controller/Modulos/compras_inventario/obtener_compra.php?id=' + compraId)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    // Llenar los campos del formulario con los datos de la compra
                    document.getElementById('numero_factura').value = data.numero_factura;
                    document.getElementById('fecha_factura').value = data.fecha_factura;
                    document.getElementById('total_neto').value = data.total_neto;
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        // Si se selecciona "Agregar una nueva Factura", los campos se dejan vacíos
        document.getElementById('numero_factura').value = '';
        document.getElementById('fecha_factura').value = '';
        document.getElementById('total_neto').value = '';
    }
});
