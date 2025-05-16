<?php
// Mostrar los ingredientes de la receta para el producto
session_start();
include("../../../conexionBD/conexion.php");

$id_producto = $_GET['id_producto'];

// Consulta 1: Ingredientes ya asignados a la receta
$sql_receta = "SELECT mp.nombre_materia_prima, r.cantidad, r.precio_unitario 
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
    <link rel="stylesheet" href="/proyecto/public/css/menu.css">
    <link rel="stylesheet" href="/proyecto/public/css/compras_inventario.css">
    <link rel="stylesheet" href="/proyecto/public/css/modal.css">

</head>
<body>
<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="categorias.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Receta</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="/proyecto/public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
</div>
    <div class="container">
        <div class="container_formulario">
            
            <!-- Formulario para crear nueva materia prima -->
            <div class="form-section">
                <h3>Crear nuevo producto</h3>
                <form action="/proyecto/controller/Modulos/compras_inventario/guardar_materiaPrima.php" method="POST">
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="number" name="precio" placeholder="Precio Venta $" required>
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
                            ?>
                        </select>
                    </div>
                    <input type="number" name="cantidad" placeholder="Cantidad" require>
                    <div class="container-button">
                        <button type="submit">Crear</button>
                    </div>
                    
                </form>
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
                        <th>Cantidad</th>
                        <th>Costo en Receta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultado_receta->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id_materia_prima']; ?></td>
                            <td><?php echo $row['nombre_materia_prima']; ?></td>
                            <td><?php echo $row['cantidad']; ?></td>
                            <td><?php echo '$' . number_format($row['cantidad'] * $row['precio_unitario'], 2); ?></td>
                            <td>
                                <a href="#">✏️</a> <!-- Editar -->
                                <a href="#">🗑️</a> <!-- Eliminar -->
                            </td>
                        </tr>
                    <?php } 
                    ?>
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

    <script src="/proyecto/public/js/compras_inventario.js"></script>
</body>
</html>