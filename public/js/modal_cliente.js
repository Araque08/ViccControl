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
      cedula: document.getElementById('cedula').value.trim(),
      telefono: document.getElementById('telefono_cliente').value.trim(),
      direccion: document.getElementById('direccion_cliente').value.trim(),
      email: document.getElementById('email_cliente').value.trim()
    };

    const productos = obtenerDatosFactura();
    const totales = obtenerTotales();
    const medioPagoValor = parseInt(document.querySelector('#medio-pago').value);

    const payload = {
      nombre_cliente: clienteData.nombre,
      cedula: clienteData.cedula,
      telefono_cliente: clienteData.telefono,
      direccion_cliente: clienteData.direccion,
      email_cliente: clienteData.email,
      subtotal: parseFloat(totales.subtotal.replace(/[$.]/g, '').replace(',', '.')),
      ipm: parseFloat(totales.igv.replace(/[$.]/g, '').replace(',', '.')),
      medio_pago: medioPagoValor,
      productos: productos,
      total: parseFloat(totales.total.replace(/[$.]/g, '').replace(',', '.'))
    };

fetch('../../../controller/Modulos/Ventas/procesar_pago.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
    if (data.status === 'success') {
      alert("✅ Venta realizada con éxito.");
      
      // Redirige a factura.php con el ID
      window.open(`/../../../modules/factura.php?id_venta=${data.venta_id}`);

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
      const idProducto = parseInt(fila.getAttribute('data-id-producto')) || null;
      const nombre = fila.children[1].textContent;
      const cantidad = parseInt(fila.querySelector('.cantidad').textContent);
      const total = parseFloat(fila.querySelector('.total-producto').textContent.replace(/[$.]/g, '').replace(',', '.'));
      const precio_unitario = total / cantidad;

      productos.push({
        id_producto: idProducto,
        nombre: nombre,
        cantidad: cantidad,
        precio_unitario: precio_unitario
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

