<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}
$_SESSION['id_categoria'] = $_GET['id_categoria'];
$sql = "SELECT 
    p.id_producto,
    p.nombre_producto,
    p.Precio_venta,
    c.nombre_categoria,
    IFNULL(SUM(r.cantidad * pp.precio_promedio), 0) AS costo_total
FROM Productos p
JOIN CategoriaProducto c ON p.fk_id_categoria = c.id_categoria
LEFT JOIN Receta r ON r.fk_id_producto = p.id_producto
LEFT JOIN PrecioPromedio pp ON pp.fk_id_materia_prima = r.fk_id_materia_prima
WHERE c.fk_id_restaurante = ? AND c.id_categoria = ?
GROUP BY p.id_producto";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $_SESSION['id_restaurante'], $_SESSION['id_categoria'] );
$stmt->execute();
$resultado = $stmt->get_result();

$nombre_categoria = 'Categoría'; // Valor por defecto


if (isset($_GET['id_categoria'])) {
    $id_categoria = intval($_GET['id_categoria']);
    $sql = "SELECT nombre_categoria FROM CategoriaProducto WHERE id_categoria = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_categoria);
    $stmt->execute();
    $stmt->bind_result($nombre_categoria);
    $stmt->fetch();
    $stmt->close();
}



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Recetas Hamburguesas</title>
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
                    <a href="categorias.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong><?php echo htmlspecialchars($nombre_categoria); ?></strong></h1>

            </div>
        </div>
        <div class="logo">
            <img src="/../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
    <?php if (isset($_GET['eliminado'])): ?>
    <div class="alert-success">Producto eliminado correctamente.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert-error">
            <?php
            switch ($_GET['error']) {
                case 'falta_id': echo "ID del producto no proporcionado."; break;
                case 'fallo': echo "Error al eliminar el producto."; break;
                default: echo "Ocurrió un error desconocido."; break;
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="container_buscar">
        <div class="add_receta">
            <a href="nueva_receta.php?id_categoria=<?php echo $_SESSION['id_categoria']; ?>">
                <i class="fa-solid fa-square-plus"></i>
            </a>
        </div>
        <table>
            <tr>
                <th>Cod</th>
                <th>Nombre</th>
                <th>Costo</th>
                <th>Precio Venta</th>
                <th>Acciones</th>
            </tr>
                <?php
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['id_producto'] . "</td>
                                <td>" . $row['nombre_producto'] . "</td>
                                <td>$" . number_format($row['costo_total'], 2) ."</td>
                                <td>$" . number_format($row['Precio_venta'], 2) . "</td>
                                <td>
                                    <a href='nueva_receta.php?id=" . $row['id_producto'] . "'>✏️</a>
                                    <a href='../../../controller/Modulos/Ventas/eliminar_producto.php?id=" . $row['id_producto'] . "' onclick=\"return confirm('¿Estás seguro de eliminar este producto? Esta acción también eliminará su receta.')\">🗑️</a>

                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr>
                            <td colspan='5' style='text-align: center;'>No hay productos registrados.</td>
                        </tr>";
                }
                ?>
        </table>
    </div>
</div>
</body>
</html>
