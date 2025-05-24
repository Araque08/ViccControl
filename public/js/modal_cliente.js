document.addEventListener('DOMContentLoaded', () => {
  const modalCliente = document.getElementById('modal-cliente');
  const btnGenerarFactura = document.querySelector('.btn-factura');
  const cerrarModal = document.getElementById('cerrar-modal');
  const formCliente = document.getElementById('form-cliente');

  btnGenerarFactura.addEventListener('click', (e) => {
    e.preventDefault();

    const filas = document.querySelectorAll("#tabla-factura tbody tr");
    if (filas.length === 0) {
      alert("Debe agregar al menos un producto para generar factura.");
      return;
    }

    modalCliente.style.display = 'block';
  });

  cerrarModal.addEventListener('click', () => {
    modalCliente.style.display = 'none';
  });

  window.addEventListener('click', (e) => {
    if (e.target === modalCliente) {
      modalCliente.style.display = 'none';
    }
  });

  formCliente.addEventListener('submit', e => {
    e.preventDefault();

    if (!formCliente.checkValidity()) {
      formCliente.reportValidity();
      return;
    }

    const clienteData = {
      nombre: document.getElementById('nombre_cliente').value.trim(),
      telefono: document.getElementById('telefono_cliente').value.trim(),
      direccion: document.getElementById('direccion_cliente').value.trim(),
      email: document.getElementById('email_cliente').value.trim()
    };

    const productos = obtenerDatosFactura();
    const totales = obtenerTotales();
    const medioPagoValor = parseInt(document.querySelector('#medio-pago').value);

    const payload = {
      nombre_cliente: clienteData.nombre,
      telefono_cliente: clienteData.telefono,
      direccion_cliente: clienteData.direccion,
      email_cliente: clienteData.email,
      subtotal: parseFloat(totales.subtotal.replace(/[$.]/g, '').replace(',', '.')),
      ipm: parseFloat(totales.igv.replace(/[$.]/g, '').replace(',', '.')),
      medio_pago: medioPagoValor,
      productos: productos,
      total: parseFloat(totales.total.replace(/[$.]/g, '').replace(',', '.'))
    };

    const facturaHTML = `
<html>
<head>
  <title>Factura de Venta</title>
  <style>
    body { font-family: 'Arial', sans-serif; font-size: 12px; width: 80mm; margin: 0 auto; color: #000; }
    .ticket-container { padding: 10px; }
    .centered { text-align: center; }
    .empresa-info, .cliente-info, .totales { margin-bottom: 10px; }
    .empresa-info strong, .cliente-info strong { display: block; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border-bottom: 1px dashed #999; padding: 4px 0; text-align: left; }
    th { font-weight: bold; }
    .totales p { margin: 3px 0; text-align: right; }
    .footer { margin-top: 10px; text-align: center; font-size: 10px; border-top: 1px dashed #ccc; padding-top: 5px; }
    .print-button { display: block; width: 100%; margin-top: 15px; padding: 6px; background-color: #000; color: #fff; border: none; cursor: pointer; font-size: 12px; }
    @media print { .print-button { display: none; } }
  </style>
</head>
<body>
  <div class="ticket-container">
    <div class="centered empresa-info">
      <strong>VICC CONTROL S.A.S</strong>
      NIT: 901.999.888-1<br>
      Calle 100 # 20-30, Bogotá<br>
      Tel: (601) 1234567
    </div>
    <hr>
    <div class="cliente-info">
      <strong>Cliente:</strong> ${clienteData.nombre}<br>
      <strong>Teléfono:</strong> ${clienteData.telefono}<br>
      <strong>Dirección:</strong> ${clienteData.direccion}<br>
      <strong>Email:</strong> ${clienteData.email}
    </div>
    <table>
      <thead>
        <tr><th>Item</th><th>Producto</th><th>Cant</th><th>Total</th></tr>
      </thead>
      <tbody>
        ${productos.map(p => `
          <tr><td>${p.item}</td><td>${p.nombre}</td><td>${p.cantidad}</td><td>${p.total}</td></tr>
        `).join('')}
      </tbody>
    </table>
    <div class="totales">
      <p><strong>Subtotal:</strong> $${totales.subtotal}</p>
      <p><strong>IVA (8%):</strong> $${totales.igv}</p>
      <p><strong>Total:</strong> <strong>$${totales.total}</strong></p>
    </div>
    <button class="print-button" onclick="window.print()">🖨️ Imprimir Factura</button>
    <div class="footer">¡Gracias por su compra!<br>Factura generada automáticamente por ViccControl.</div>
  </div>
</body>
</html>`;

    fetch('../../../controller/Modulos/Ventas/procesar_pago.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        alert("✅ Venta realizada con éxito.");
        const ventanaFactura = window.open('', '_blank', 'width=800,height=600');
        if (ventanaFactura) {
          setTimeout(() => {
            ventanaFactura.document.write(facturaHTML);
            ventanaFactura.document.close();
          }, 200);
        }

        document.querySelector("#tabla-factura tbody").innerHTML = "";
        document.getElementById("subtotal").textContent = "$0";
        document.getElementById("ipm").textContent = "$0";
        document.getElementById("total").textContent = "$0";
        formCliente.reset();
      } else {
        alert("Error registrando la venta: " + data.message);
      }
    })
    .catch(error => {
      console.error("❌ Error en la transacción:", error);
      alert("Ocurrió un error al registrar la factura.");
      console.log("📦 Payload enviado:", payload);
    });

    modalCliente.style.display = 'none';
  });

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

