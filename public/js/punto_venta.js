document.addEventListener('DOMContentLoaded', () => {
    const categoriaCards = document.querySelectorAll('.categoria-card');
    const categoriasContainer = document.querySelector('.categorias-container');
    const productosContainer = document.getElementById('productos-de-categoria');
    const btnVolver = document.getElementById('volver-categorias');

    let contadorItem = 1;

    // Referencias a los elementos de totales y checkbox de propina
    const subtotalElem = document.getElementById("subtotal");
    const ipmElem = document.getElementById("ipm");
    const totalElem = document.getElementById("total");
    const checkboxPropina = document.querySelector('input[type="checkbox"]');
    const btnLimpiar = document.querySelector('.btn-limpiar');

    categoriaCards.forEach(card => {
        card.addEventListener('click', () => {
            const idCategoria = card.getAttribute('data-id');

            categoriasContainer.style.display = 'none';
            btnVolver.style.display = 'block';
            productosContainer.style.display = 'flex';

            fetch(`../../../controller/Modulos/Ventas/obtener_producto_categoria.php?id_categoria=${idCategoria}`)
                .then(response => response.json())
                .then(data => {
                    productosContainer.innerHTML = '';

                    if (data.length === 0) {
                        productosContainer.innerHTML = '<p>No hay productos para esta categoría.</p>';
                        return;
                    }

                    data.forEach(producto => {
                        const div = document.createElement('div');
                        div.classList.add('producto-card');
                        div.innerHTML = `
                            <img src="../../../public/img/${producto.imagen_producto}" alt="${producto.nombre_producto}">
                            <p>${producto.nombre_producto}</p>
                            <p><strong>$${parseFloat(producto.Precio_venta).toLocaleString()}</strong></p>
                            <button onclick='agregarProducto("${producto.id_producto}", "${producto.nombre_producto}", ${producto.Precio_venta})'>Agregar</button>
                        `;
                        productosContainer.appendChild(div);
                    });
                })
                .catch(err => {
                    productosContainer.innerHTML = '<p>Error cargando productos.</p>';
                    console.error(err);
                });
        });
    });

    btnVolver.addEventListener('click', () => {
        productosContainer.innerHTML = '';
        categoriasContainer.style.display = 'flex';
        productosContainer.style.display = 'none';
        btnVolver.style.display = 'none';
    });

    window.agregarProducto = function(id, nombre, precio) {
        const tbody = document.querySelector("#tabla-factura tbody");

        const fila = document.createElement('tr');
        fila.setAttribute('data-id-producto', id); // <---- ASÍ

        fila.innerHTML = `
            <td>${contadorItem.toString().padStart(2, '0')}</td>
            <td>${nombre}</td>
            <td>
                <button class="btn-restar">-</button>
                <span class="cantidad">1</span>
                <button class="btn-sumar">+</button>
            </td>
            <td class="total-producto">$${precio.toLocaleString()}</td>
            <td>
                <i class="fa-solid fa-trash btn-eliminar" style="cursor:pointer;"></i>
            </td>
        `;

        tbody.appendChild(fila);
        contadorItem++;

        function actualizarTotalesFila() {
            const cantidad = parseInt(fila.querySelector('.cantidad').textContent);
            const totalProducto = fila.querySelector('.total-producto');
            const nuevoTotal = precio * cantidad;
            totalProducto.textContent = `$${nuevoTotal.toLocaleString()}`;
            actualizarTotales();
        }

        fila.querySelector('.btn-sumar').addEventListener('click', () => {
            const cantidadElem = fila.querySelector('.cantidad');
            cantidadElem.textContent = parseInt(cantidadElem.textContent) + 1;
            actualizarTotalesFila();
        });

        fila.querySelector('.btn-restar').addEventListener('click', () => {
            const cantidadElem = fila.querySelector('.cantidad');
            const current = parseInt(cantidadElem.textContent);
            if (current > 1) {
                cantidadElem.textContent = current - 1;
                actualizarTotalesFila();
            }
        });

        fila.querySelector('.btn-eliminar').addEventListener('click', () => {
            fila.remove();
            actualizarTotales();
            contadorItem--;
            const filas = tbody.querySelectorAll('tr');
            filas.forEach((row, index) => {
                row.children[0].textContent = (index + 1).toString().padStart(2, '0');
            });
        });

        // 🔽 Actualizar los totales generales
        function actualizarTotales() {
            const filas = tbody.querySelectorAll('tr');
            let subtotal = 0;
            filas.forEach(fila => {
                const totalText = fila.querySelector('.total-producto').textContent;
                const totalNum = parseFloat(totalText.replace(/[$.]/g, '').replace(',', '.')) || 0;
                subtotal += totalNum;
            });

            let propina = checkboxPropina.checked ? subtotal * 0.10 : 0;
            const igv = subtotal * 0.18;
            const total = subtotal + igv + propina;

            subtotalElem.textContent = `$${subtotal.toLocaleString()}`;
            ipmElem.textContent = `$${igv.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
            totalElem.textContent = `$${total.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
        }

        // Inicializar totales
        actualizarTotales();
    };

    // 🔽 Delegación de evento para eliminar filas dinámicamente
    document.querySelector("#tabla-factura tbody").addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-eliminar')) {
            const fila = e.target.closest('tr');
            fila.remove();
            contadorItem--;
            // Reajustar numeración de filas
            const filas = this.querySelectorAll('tr');
            filas.forEach((row, index) => {
                row.children[0].textContent = (index + 1).toString().padStart(2, '0');
            });
            // Actualizar totales después de eliminar
            const subtotalElem = document.getElementById("subtotal");
            const ipmElem = document.getElementById("ipm");
            const totalElem = document.getElementById("total");

            let subtotal = 0;
            const filasActuales = this.querySelectorAll('tr');
            filasActuales.forEach(fila => {
                const totalText = fila.querySelector('.total-producto').textContent;
                const totalNum = parseFloat(totalText.replace(/[$.]/g, '').replace(',', '.')) || 0;
                subtotal += totalNum;
            });

            let propina = checkboxPropina.checked ? subtotal * 0.10 : 0;
            const igv = subtotal * 0.18;
            ipmElem.textContent = `$${igv.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
            subtotalElem.textContent = `$${subtotal.toLocaleString()}`;
            const total = subtotal + igv + propina;
            totalElem.textContent = `$${total.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
        }
    });

    // Escuchar cambio en checkbox de propina
    checkboxPropina.addEventListener('change', () => {
        // Actualizar totales cuando el checkbox cambia
        const filas = document.querySelectorAll("#tabla-factura tbody tr");
        if (filas.length === 0) return; // No actualizar si no hay filas
        let subtotal = 0;
        filas.forEach(fila => {
            const totalText = fila.querySelector('.total-producto').textContent;
            const totalNum = parseFloat(totalText.replace(/[$.]/g, '').replace(',', '.')) || 0;
            subtotal += totalNum;
        });
        let propina = checkboxPropina.checked ? subtotal * 0.10 : 0;
        const igv = subtotal * 0.08;
        const total = subtotal + igv + propina;

        document.getElementById("subtotal").textContent = `$${subtotal.toLocaleString()}`;
        document.getElementById("ipm").textContent = `$${igv.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
        document.getElementById("total").textContent = `$${total.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
    });

    // 🔽 Agregado: botón limpiar para borrar lista y resetear totales y contador
    btnLimpiar.addEventListener('click', () => {
        const tbody = document.querySelector("#tabla-factura tbody");
        tbody.innerHTML = ''; // Borrar productos
        contadorItem = 1;     // Reset contador
        subtotalElem.textContent = '$0';
        ipmElem.textContent = '$0';
        totalElem.textContent = '$0';
    });

    
});
