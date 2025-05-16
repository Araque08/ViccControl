document.getElementById('compra').addEventListener('change', function () {
    const compraId = this.value;

    if (compraId === 'nueva') {
        // Limpiar campos si se va a crear una nueva factura
        document.getElementById('proveedor').value = '';
        document.getElementById('numero_factura').value = '';
        document.getElementById('fecha_factura').value = '';
        document.getElementById('total_factura').value = '';
        document.querySelector('#materiaPrimaTable tbody').innerHTML = '';
        document.querySelector('#materiaPrimaTable tfoot').innerHTML = '';
        return;
    }

    if (compraId !== 'nada') {
        fetch('/../../controller/Modulos/compras_inventario/obtener_compra.php?id=' + compraId)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('proveedor').value = data.id_proveedor;
                    document.getElementById('numero_factura').value = data.numero_factura;
                    document.getElementById('fecha_factura').value = data.fecha_factura;
                    document.getElementById('total_factura').value = data.total_neto;

                    // Limpiar la tabla antes de llenarla
                    const tbody = document.querySelector('#materiaPrimaTable tbody');
                    const tfoot = document.querySelector('#materiaPrimaTable tfoot');
                    tbody.innerHTML = '';
                    tfoot.innerHTML = '';

                    let contador = 1;
                    let total = 0;

                    data.detalles.forEach(detalle => {
                        const fila = document.createElement('tr');
                        fila.innerHTML = `
                            <td>${contador++}</td>
                            <td>${detalle.producto}</td>
                            <td>${data.numero_factura}</td>
                            <td>${detalle.cantidad}</td>
                            <td>${detalle.precio}</td> 
                            <td>${data.fecha_factura}</td>  
                            <td>${data.nombre_proveedor}</td>
                            <td><a href="#">Editar</a> | <a href="#">Eliminar</a></td>
                        `;
                        tbody.appendChild(fila);

                        total += parseFloat(detalle.precio);
                    });

                    // Mostrar total en el footer
                    tfoot.innerHTML = `
                        <tr>
                            <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
                            <td><strong>$${total.toFixed(2)}</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    `;

                    // Mostrar mensaje flotante de validación
                    const totalFactura = parseFloat(data.total_neto);
                    const mensajeDiv = document.getElementById('mensaje-validacion');

                    if (Math.abs(total - totalFactura) < 0.01) {
                        mensajeDiv.textContent = "✅ La suma de los productos coincide con el total de la factura.";
                        mensajeDiv.style.backgroundColor = "#d4edda";
                        mensajeDiv.style.color = "#155724";
                    } else {
                        mensajeDiv.textContent = "⚠️ El total de la factura NO coincide con la suma de los productos.";
                        mensajeDiv.style.backgroundColor = "#f8d7da";
                        mensajeDiv.style.color = "#721c24";
                    }

                    mensajeDiv.style.display = "block";
                    setTimeout(() => {
                        mensajeDiv.style.display = "none";
                    }, 4000);
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        document.getElementById('proveedor').value = '';
        document.getElementById('numero_factura').value = '';
        document.getElementById('fecha_factura').value = '';
        document.getElementById('total_factura').value = '';
        document.querySelector('#materiaPrimaTable tbody').innerHTML = '';
        document.querySelector('#materiaPrimaTable tfoot').innerHTML = '';
    }
});



