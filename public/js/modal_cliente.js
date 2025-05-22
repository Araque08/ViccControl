document.addEventListener('DOMContentLoaded', () => {
  const modalCliente = document.getElementById('modal-cliente');
  const btnGenerarFactura = document.querySelector('.btn-factura');
  const cerrarModal = document.getElementById('cerrar-modal');
  const formCliente = document.getElementById('form-cliente');

  btnGenerarFactura.addEventListener('click', (e) => {
    e.preventDefault();

    // Verifica que haya productos en la factura
    const filas = document.querySelectorAll("#tabla-factura tbody tr");
    if (filas.length === 0) {
      alert("Debe agregar al menos un producto para generar factura.");
      return;
    }

    // Mostrar modal
    modalCliente.style.display = 'block';
  });

  // Cerrar modal con la X
  cerrarModal.addEventListener('click', () => {
    modalCliente.style.display = 'none';
  });

  // Cerrar modal si haces click fuera del contenido
  window.addEventListener('click', (e) => {
    if (e.target === modalCliente) {
      modalCliente.style.display = 'none';
    }
  });

  // Evento submit para procesar datos cliente y generar factura
  formCliente.addEventListener('submit', e => {
    e.preventDefault();

    // Usar el validador HTML nativo para verificar campos required
    if (!formCliente.checkValidity()) {
      formCliente.reportValidity(); // Esto muestra mensajes nativos
      return;
    }

    const clienteData = {
      nombre: document.getElementById('nombre_cliente').value.trim(),
      telefono: document.getElementById('telefono_cliente').value.trim(),
      direccion: document.getElementById('direccion_cliente').value.trim(),
      email: document.getElementById('email_cliente').value.trim()
    };

    // Solo comprobamos campos obligatorios (nombre y teléfono)
    if (!clienteData.nombre || !clienteData.telefono) {
      alert("Por favor, complete los campos obligatorios.");
      return;
    }

    // Ocultar modal
    modalCliente.style.display = 'none';

    // Obtener datos factura y totales
    const productos = obtenerDatosFactura();
    const totales = obtenerTotales();

    // Generar factura en nueva ventana
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

  // Funciones auxiliares que debes tener definidas en tu JS
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

  function obtenerTotales() {
    return {
      subtotal: document.getElementById("subtotal").textContent,
      igv: document.getElementById("ipm").textContent,
      total: document.getElementById("total").textContent
    };
  }
});
