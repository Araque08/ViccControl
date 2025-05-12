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


$sql_proveedores = "SELECT id_proveedor, nombre_proveedor FROM Proveedores WHERE fk_id_restaurante = ?";
$stmt = $conexion->prepare($sql_proveedores);
$stmt->bind_param("i", $_SESSION['id_restaurante']);
$stmt->execute();
$result_proveedores = $stmt->get_result();

// Consulta para obtener las materias primas asociadas al restaurante
$sql = "SELECT id_materia_prima, nombre_materia_prima FROM MateriaPrima WHERE fk_id_restaurante = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_restaurante);  // Usamos el ID del restaurante desde la sesión
$stmt->execute();
$result = $stmt->get_result();

// Consulta para obtener las compras y los detalles de las compras
$sql_compras = "
    SELECT 
        c.id_compra, 
        c.fk_id_proveedor, 
        c.fecha_compra, 
        c.totalcompra, 
        p.nombre_proveedor,
        dc.fk_id_materia_prima, 
        mp.nombre_materia_prima, 
        dc.cantidad
    FROM Compras c
    JOIN Proveedores p ON c.fk_id_proveedor = p.id_proveedor
    JOIN DetalleCompra dc ON c.id_compra = dc.fk_id_compra
    JOIN MateriaPrima mp ON dc.fk_id_materia_prima = mp.id_materia_prima
    WHERE c.fk_id_restaurante = ?";

$stmt_compras = $conexion->prepare($sql_compras);
$stmt_compras->bind_param("i", $_SESSION['id_restaurante']);
$stmt_compras->execute();
$result_compras = $stmt_compras->get_result();



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
                <h1 class="nombre-pagina"><strong>Compras</strong></h1>
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
                <div>
                        <select name="compra" id="compra" required>
                            <option value="nada">Seleccione una opcion</option>
                            <?php
                            // Verificar si hay compras
                            if ($result_compras->num_rows > 0) {
                                while ($row = $result_compras->fetch_assoc()) {
                                    // Mostrar id_compra y nombre_proveedor en las opciones
                                    echo '<option value="' . $row['id_compra'] . '">' . $row['id_compra'] . ' - ' . $row['nombre_proveedor'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay compras realizadas</option>';
                            }
                            ?>
                            <option value="">Agregar una nueva Factura</option>
                        </select>     
                </div>
                <form action="/../../../controller/Modulos/compras_inventario/guardar_compra.php" method="POST">
                    <div>
                        <select name="proveedor" id="categoria" required>
                            <option value="">Seleccionar proveedor</option>
                            <?php
                            // Verificar si hay proveedores disponibles
                            if ($result_proveedores->num_rows > 0) {
                                while ($row = $result_proveedores->fetch_assoc()) {
                                    echo '<option value="' . $row['id_proveedor'] . '">' . $row['nombre_proveedor'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay proveedores disponibles</option>';
                            }
                            ?>
                        </select>
                        <a href="../clientes_proveedores/proveedores.php">
                            <button type="button">+ Proveedores</button>
                        </a>

                    </div>
                    <input type="text" name="numero_factura" id="numero_factura" placeholder="Numero Factura" required> 
                    <input type="date" name="fecha_factura" placeholder="Unidad Medida" required>       
                    <input type="number" name="total_neto" placeholder="Total Neto" required>
                    <!-- Categoría Dropdown o botón para agregar nueva categoría -->
                    <div>
                        <select name="materia_prima[]" id="categoria" required>
                            <option value="">Seleccionar Producto</option>
                                <?php 
                                // Verificar si hay materias primas
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['id_materia_prima'] . '">' . $row['nombre_materia_prima'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No hay materias primas disponibles</option>';
                                }
                                ?>
                        </select>
                        <a href="materia_prima.php">
                            <button type="button">+ Materia Prima</button>
                        </a>
                    </div>
                    <input type="number" name="cantidad[]" placeholder="Cantidad" required>
                    <input type="number" name="precio_neto[]" placeholder="Precio Neto" required>
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
                        <th>Producto</th>
                        <th>Num Docu</th>
                        <th>Cntd</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                     <?php 
                        // Variable para el contador de elementos
                        $counter = 1;
                        
                        // Verificar si hay compras disponibles
                        if ($result_compras->num_rows > 0) {
                            while ($compra = $result_compras->fetch_assoc()) {
                                echo '<tr>';
                                
                                // Mostrar el número secuencial en lugar del id_compra
                                echo '<td>' . $counter . '</td>';
                                
                                // Mostrar los demás datos
                                echo '<td>' . $compra['nombre_materia_prima'] . '</td>';
                                echo '<td>' . $compra['id_compra'] . '</td>';
                                echo '<td>' . $compra['cantidad'] . '</td>';
                                echo '<td>' . $compra['fecha_compra'] . '</td>';
                                echo '<td>' . $compra['nombre_proveedor'] . '</td>';
                                echo '<td><a href="#">Editar</a> | <a href="#">Eliminar</a></td>';
                                
                                echo '</tr>';

                                // Incrementar el contador para el siguiente número
                                $counter++;
                            }
                        } else {
                            echo '<tr><td colspan="7">No hay compras registradas.</td></tr>';
                        }
                        ?>             
                </tbody>
            </table>
        </div>  
    </div>

    <script src="/../../../public/js/cargar_datos_compra.js"></script>
</body>
</html>