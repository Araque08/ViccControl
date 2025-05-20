<?php
// Mostrar los ingredientes de la receta para el producto
session_start();
include("../../../conexionBD/conexion.php");


// Consulta 1: Ingredientes ya asignados a la receta
$id_producto = $_GET['id'];

$sql_receta = "SELECT 
    mp.id_materia_prima,
    mp.nombre_materia_prima,
    mp.stock_disp AS existencia,
    mp.unidad_materia_prima AS Unidad,
    r.cantidad,
    r.precio_unitario,
    (r.cantidad * r.precio_unitario) AS Costo_receta
FROM Receta r
JOIN MateriaPrima mp ON r.fk_id_materia_prima = mp.id_materia_prima
WHERE r.fk_id_producto = ?";
$stmt_receta = $conexion->prepare($sql_receta);
$stmt_receta->bind_param("i", $id_producto);
$stmt_receta->execute();
$resultado_receta = $stmt_receta->get_result();


// Consulta 2: Materias primas disponibles del restaurante
$sql_ingredientes = "SELECT * FROM MateriaPrima WHERE fk_id_restaurante = ?";
$stmt_materia = $conexion->prepare($sql_ingredientes);
$stmt_materia->bind_param("i", $_SESSION['id_restaurante']);
$stmt_materia->execute();
$materia_prima = $stmt_materia->get_result();

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Receta</title>
    <link rel="stylesheet" href="/../../../public/css/menu.css">
    <link rel="stylesheet" href="/../../../public/css/ventas.css">
    <link rel="stylesheet" href="/../../../public/css/modal.css">

</head>
<body>
<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="ver_categoria.php?id_categoria=<?php echo $_SESSION['id_categoria']; ?>">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Receta</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="/../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
</div>
    <div class="container">
        <div class="container_formulario">
            <!-- Formulario para crear nueva materia prima -->
            <div class="form-section">
                    <form action="/../../../controller/Modulos/ventas/agregar_nueva_receta.php" method="POST"  enctype="multipart/form-data">
                        <h3>Crear nuevo producto</h3>
                        <input type="hidden" name="id_categoria" value="<?= htmlspecialchars($_SESSION['id_categoria']) ?>">

                        <input type="text" name="nombre" placeholder="Nombre" id="nombre" required>
                        <input type="number" name="precio" placeholder="Precio Venta $" id="precio" required>
                        <!-- Categoría Dropdown o botón para agregar nueva categoría -->
                        <div>
                            <select name="materia_prima[]" id="categoria" required>
                                <option value="">Seleccionar Producto</option>
                                <?php 
                                // Mostrar en el <select> las materias primas
                                if ($materia_prima->num_rows > 0) {
                                    while ($row = $materia_prima->fetch_assoc()) {
                                        echo '<option value="' . $row['id_materia_prima'] . '">' . $row['nombre_materia_prima'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No hay materias primas disponibles</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <input type="number" name="cantidad[]" placeholder="Cantidad" require>
                        <input type="file" name="imagen_producto" accept="image/*">

                        <div class="container-button">
                            <button type="submit">Crear</button>
                        </div>
                    </form>
            </div>
            <div class="imagen_receta">
                <div id="imagen_producto" class="imagen_producto"></div>
            </div>
        </div>
        
        <div class="container_buscar">
            <!-- Buscar por primera letra -->
            <div class="search-section">
                <h3>Buscar por nombre</h3>
                <input type="text" id="search" placeholder="Buscar...">
                <button id="searchBtn">Buscar</button>
            </div>

            <!-- Tabla con la lista de materias primas -->
            <table id="materiaPrimaTable">
                <thead>
                    <tr>
                        <th>Cod</th>
                        <th>Nombre de Ingrediente</th>
                        <th>Existencia</th>
                        <th>Cantidad</th>
                        <th>Costo en Receta</th>
                        <th>Unidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado_receta->num_rows > 0): ?>
                        <?php while ($row = $resultado_receta->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['id_materia_prima'] ?></td>
                                <td><?= $row['nombre_materia_prima'] ?></td>
                                <td><?= $row['existencia'] ?></td>
                                <td><?= $row['cantidad'] ?></td>
                                <td>$<?= number_format($row['Costo_receta'], 2) ?></td>
                                <td><?= $row['Unidad'] ?></td>
                                <td>
                                    <a href="#">✏️</a>
                                    <a href="#">🗑️</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No hay ingredientes en esta receta.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal para agregar nueva categoría -->
   <div id="nuevaCategoriaModal" class="modal">
        <div class="modal-content">
            <span class="cerrar-modal">&times;</span>
            <div id="contenidoNuevaCategoria"></div>
        </div>
    </div>

    <script src="/../../../public/js/compras_inventario.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            const idProducto = urlParams.get("id");

            if (idProducto) {
                fetch(`/../../../controller/Modulos/ventas/obtener_producto.php?id=${idProducto}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            // Llenar inputs
                            document.getElementById('nombre').value = data.nombre_producto;
                            document.getElementById('precio').value = data.Precio_venta;

                            // Imagen actual
                            if (data.imagen_producto) {
                                const imagenPreview = document.createElement("p");
                                imagenPreview.innerHTML = '<img src="/../../../public/img/' + data.imagen_producto + '" width="200">';
                                const fileInput = document.getElementById("imagen_producto");
                                fileInput.parentNode.insertBefore(imagenPreview, fileInput);
                            }

                            // Si quieres manejar materias primas también puedes añadir inputs aquí
                        }
                    })
                    .catch(error => console.error("Error al obtener el producto:", error));
            }
        });
    </script>
</body>
</html>