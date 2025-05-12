<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

include("../../../conexionBD/conexion.php");

// ⏱ Tiempo límite de inactividad (en segundos)
$tiempo_limite = 1200;

if (isset($_SESSION['ultimo_acceso'])) {
    $inactividad = time() - $_SESSION['ultimo_acceso'];
    if ($inactividad > $tiempo_limite) {
        session_unset();
        session_destroy();
        header("Location: ../../../index.php?expirada=1");
        exit;
    }
}
$_SESSION['ultimo_acceso'] = time();

$id_restaurante = $_SESSION['id_restaurante'];

// Consulta para obtener las categorías del restaurante

// Consulta para obtener las materias primas del restaurante
$sql_materia_prima = "SELECT mp.id_materia_prima, mp.nombre_materia_prima, mp.unidad_materia_prima, 
                            cm.nombre_categoria_materia, mp.stock_min, mp.descripcion_materia_prima 
                        FROM MateriaPrima mp
                        JOIN CategoriaMateriaPrima cm ON mp.fk_id_categoria_materia = cm.id_categoria_materia
                        WHERE mp.fk_id_restaurante = ?";
$stmt = $conexion->prepare($sql_materia_prima);
$stmt->bind_param("i", $id_restaurante);
$stmt->execute();
$materias_primas_result = $stmt->get_result();  // Almacenar resultado de materia prima

// Consulta para obtener las categorías del restaurante
$sql_categoria = "SELECT * FROM CategoriaMateriaPrima WHERE fk_id_restaurante = ?";
$stmt = $conexion->prepare($sql_categoria);
$stmt->bind_param("i", $id_restaurante);
$stmt->execute();
$categorias_result = $stmt->get_result();  // Almacenar resultado de categorías

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Modulo de Control</title>
    <link rel="stylesheet" href="../../../public/css/menu.css">
    <link rel="stylesheet" href="../../../public/css/compras_inventario.css">
    <link rel="stylesheet" href="../../../public/css/modal.css">
</head>
<body>
<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="compras_inventario_menu.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Materia Prima</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
<!--Formulario compras -->

    <div class="container">
        <div class="container_formulario">
            
            <!-- Formulario para crear nueva materia prima -->
            <div class="form-section">
                <h3>Crear nueva materia prima</h3>
                <form action="/../../../controller/Modulos/compras_inventario/guardar_materiaPrima.php" method="POST">
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="text" name="unidad" placeholder="Unidad Medida" required>
                    <!-- Categoría Dropdown o botón para agregar nueva categoría -->
                    <div>
                        <select name="categoria" id="categoria" required>
                            <option value="">Seleccionar Categoría</option>
                            <?php 
                            while ($row = $categorias_result->fetch_assoc()) {
                                echo '<option value="' . $row['id_categoria_materia'] . '">' . $row['nombre_categoria_materia'] . '</option>';
                            }
                            ?>
                        </select>
                        <button onclick=" mostrarNuevaCategoria(<?= $_SESSION['id_restaurante'] ?>)">+ Categoría</button>
                    </div>
                    <input type="number" name="stock_min" placeholder="Stock mínimo" require>
                    <textarea class="description" name="descripcion" placeholder="Descripción" rows="4" ></textarea>
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
                        <th>Nombre</th>
                        <th>Unidad</th>
                        <th>Categoría</th>
                        <th>Stock min</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se deben cargar las materias primas desde la base de datos -->
                    <?php
                    
                    while ($row = $materias_primas_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['id_materia_prima'] . "</td>
                                <td>" . $row['nombre_materia_prima'] . "</td>
                                <td>" . $row['unidad_materia_prima'] . "</td>
                                <td>" . $row['nombre_categoria_materia'] . "</td>
                                <td>" . $row['stock_min'] . "</td>
                                <td>" . $row['descripcion_materia_prima'] . "</td>
                                <td>
                                    <a href='editar_materia.php?id=" . $row['id_materia_prima'] . "'>✏️</a>
                                    <a href='eliminar_materia.php?id=" . $row['id_materia_prima'] . "'>🗑️</a>
                                </td>
                            </tr>";}
                    ?>
                    <!-- Más filas se agregarían dinámicamente -->
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

    <script src="../../../public/js/compras_inventario.js"></script>
</body>
</html>
