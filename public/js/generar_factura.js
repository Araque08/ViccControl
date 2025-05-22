document.addEventListener('DOMContentLoaded', () => {
    const modalCliente = document.getElementById('modal-cliente');

    // Función para obtener los datos de la tabla factura en formato JSON
    function obtenerDatosFactura() {
        const filas = document.querySelectorAll("#tabla-factura tbody tr");
        let productos = [];
        filas.forEach(fila => {
            productos.push({
                item: fila.children[0].textContent,
                nombre: fila.children[1].textContent,
                cantidad: fila.querySelector('.cantidad').textContent,
                total: fila.querySelector('.total-producto').textContent
            });
        });
        return productos;
    }

    // Función para obtener totales
    function obtenerTotales() {
        return {
            subtotal: document.getElementById("subtotal").textContent,
            igv: document.getElementById("ipm").textContent,
            total: document.getElementById("total").textContent
        };
    }

    // Modificar el submit del modal para generar la factura
    modalCliente.querySelector('#form-cliente').addEventListener('submit', e => {
        e.preventDefault();

        const clienteData = {
            nombre: document.getElementById('nombre_cliente').value.trim(),
            telefono: document.getElementById('telefono_cliente').value.trim(),
            direccion: document.getElementById('direccion_cliente').value.trim(),
            email: document.getElementById('email_cliente').value.trim()
        };

        if (!clienteData.nombre || !clienteData.telefono) {
            alert("Por favor, complete los campos obligatorios.");
            return;
        }

        modalCliente.style.display = 'none';

        const productos = obtenerDatosFactura();
        const totales = obtenerTotales();

        // Crear ventana nueva con contenido de factura
        const facturaHTML = `
            <html>
            <head>
                <title>Factura</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h2 { text-align: center; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .totales { margin-top: 20px; float: right; }
                    .cliente-info { margin-bottom: 20px; }
                    .print-button { margin-top: 20px; }
                </style>
            </head>
            <body>
                <h2>Factura de Venta</h2>
                <div class="cliente-info">
                    <strong>Cliente:</strong> ${clienteData.nombre} <br>
                    <strong>Teléfono:</strong> ${clienteData.telefono} <br>
                    <strong>Dirección:</strong> ${clienteData.direccion} <br>
                    <strong>Email:</strong> ${clienteData.email}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${productos.map(p => `
                            <tr>
                                <td>${p.item}</td>
                                <td>${p.nombre}</td>
                                <td>${p.cantidad}</td>
                                <td>${p.total}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div class="totales">
                    <p><strong>Subtotal:</strong> ${totales.subtotal}</p>
                    <p><strong>IGV:</strong> ${totales.igv}</p>
                    <p><strong>Total:</strong> ${totales.total}</p>
                </div>
                <button class="print-button" onclick="window.print()">Imprimir Factura</button>
            </body>
            </html>
        `;

        const ventanaFactura = window.open('', '_blank', 'width=800,height=600');
        ventanaFactura.document.write(facturaHTML);
        ventanaFactura.document.close();
    });
});


