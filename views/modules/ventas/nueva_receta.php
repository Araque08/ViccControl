<?php
session_start();
include("../../../conexionBD/conexion.php");

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

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : null;
$resultado_receta = null;

if ($id_producto !== null) {
    $sql_receta = "SELECT 
    mp.id_materia_prima,
    mp.nombre_materia_prima,
    mp.stock_disp AS existencia,
    mp.unidad_materia_prima AS Unidad,
    r.cantidad,
    COALESCE(pp.precio_promedio, 0) AS precio_promedio,
    (r.cantidad * COALESCE(pp.precio_promedio, 0)) AS Costo_receta
FROM Receta r
JOIN MateriaPrima mp ON r.fk_id_materia_prima = mp.id_materia_prima
LEFT JOIN PrecioPromedio pp ON pp.fk_id_materia_prima = mp.id_materia_prima
WHERE r.fk_id_producto = ?";

    
    $stmt_receta = $conexion->prepare($sql_receta);
    $stmt_receta->bind_param("i", $id_producto);
    $stmt_receta->execute();
    $resultado_receta = $stmt_receta->get_result();
    
}
    $total_receta = 0;
    $filas_receta = [];

    if ($resultado_receta && $resultado_receta->num_rows > 0) {
        while ($row = $resultado_receta->fetch_assoc()) {
            $total_receta += $row['cantidad'] * $row['precio_promedio'];
            $filas_receta[] = $row;
        }
    }


// Obtener las materias primas del restaurante
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
    <title>Receta</title>
    <link rel="stylesheet" href="/../../../public/css/menu.css">
    <link rel="stylesheet" href="/../../../public/css/ventas.css">
    <link rel="stylesheet" href="/../../../public/css/modal.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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

    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="alert-error">⚠️ Este ingrediente ya fue agregado a la receta.</div>
    <?php endif; ?>

<div class="container">
    <div class="container_formulario">
        <div class="form-section">
            <form action="/../../../controller/Modulos/Ventas/agregar_nueva_receta.php" method="POST" enctype="multipart/form-data">
                <h3>Crear nuevo producto</h3>
                <input type="hidden" name="id_categoria" value="<?= htmlspecialchars($_SESSION['id_categoria']) ?>">
                <input type="text" name="nombre" placeholder="Nombre" id="nombre" required>
                <input type="number" name="precio" placeholder="Precio Venta $" id="precio" required>
                
                <div>
                    <select name="materia_prima[]" id="categoria" required>
                        <option value="">Seleccionar Producto</option>
                        <?php 
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

                <input type="number" name="cantidad[]" placeholder="Cantidad" step="any" required>
                <input type="file" name="imagen_producto" accept="image/*">
                <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
                
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
        <div class="search-section">
            <h3>Buscar por nombre</h3>
            <input type="text" id="search" placeholder="Buscar...">
            <button id="searchBtn">Buscar</button>
        </div>

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
                <?php if (!empty($filas_receta)): ?>
                    <?php foreach ($filas_receta as $row) { ?>
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
                <tr style="font-weight: bold;">
                        <td colspan="4" style="text-align: right;">Total Costo Receta:</td>
                        <td>$<?= number_format($total_receta, 2) ?></td>
                        <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script src="/../../../public/js/editar_producto.js"></script>
</body>
</html>
