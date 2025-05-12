<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener todos los productos y sumar el costo de sus recetas
$sql = "
SELECT 
    p.id_producto, 
    p.nombre_producto, 
    p.Precio_venta,
    IFNULL(SUM(r.cantidad * mp.Precio_venta), 0) AS costo_receta
FROM Productos p
LEFT JOIN Receta r ON p.id_producto = r.fk_id_producto
LEFT JOIN MateriaPrima mp ON r.fk_id_materiaprima = mp.id_materiaPrima
GROUP BY p.id_producto, p.nombre_producto, p.Precio_venta
";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recetas Hamburguesas</title>
    <style>
        table { width: 80%; margin: auto; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        a.edit-icon { text-decoration: none; font-size: 1.2em; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Recetas Hamburguesas</h1>
    <table>
        <tr>
            <th>Cod</th>
            <th>Nombre</th>
            <th>Costo</th>
            <th>Precio Venta</th>
            <th>Acciones</th>
        </tr>

        <?php
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>
                <td>{$fila['id_producto']}</td>
                <td>{$fila['nombre_producto']}</td>
                <td>$" . number_format($fila['costo_receta'], 0, ',', '.') . "</td>
                <td>$" . number_format($fila['precioVenta'], 0, ',', '.') . "</td>
                <td>
                    <a class='edit-icon' href='agregar_receta.php?id_producto={$fila['id_producto']}'>✏️</a>
                </td>
            </tr>";
        }
        ?>
    </table>
</body>
</html>
<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener todos los productos y sumar el costo de sus recetas
$sql = "
SELECT 
    p.id_producto, 
    p.nombre_producto, 
    p.precioVenta,
    IFNULL(SUM(r.cantidad * mp.precio_unitario), 0) AS costo_receta
FROM Productos p
LEFT JOIN Receta r ON p.id_producto = r.fk_id_producto
LEFT JOIN MateriaPrima mp ON r.fk_id_materiaprima = mp.id_materiaPrima
GROUP BY p.id_producto, p.nombre_producto, p.precioVenta
";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recetas Hamburguesas</title>
    <style>
        table { width: 80%; margin: auto; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        a.edit-icon { text-decoration: none; font-size: 1.2em; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Recetas Hamburguesas</h1>
    <table>
        <tr>
            <th>Cod</th>
            <th>Nombre</th>
            <th>Costo</th>
            <th>Precio Venta</th>
            <th>Acciones</th>
        </tr>

        <?php
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>
                <td>{$fila['id_producto']}</td>
                <td>{$fila['nombre_producto']}</td>
                <td>$" . number_format($fila['costo_receta'], 0, ',', '.') . "</td>
                <td>$" . number_format($fila['precioVenta'], 0, ',', '.') . "</td>
                <td>
                    <a class='edit-icon' href='agregar_receta.php?id_producto={$fila['id_producto']}'>✏️</a>
                </td>
            </tr>";
        }
        ?>
    </table>
</body>
</html>
