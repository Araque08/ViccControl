<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

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

$id_producto = intval($_GET['id_producto'] ?? 0);

// Consultar nombre del producto
$stmt = $conexion->prepare("SELECT nombre_producto, precio_venta FROM Productos WHERE id_producto = ?");
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta - <?= htmlspecialchars($producto['nombre_producto']) ?></title>
</head>
<body>
    <!--<h2>Receta de: <?= htmlspecialchars($producto['nombre_producto']) ?> (Precio: $<?= $producto['precioVenta'] ?>)</h2>-->

    <!-- Formulario para agregar ingrediente -->
    <form action="guardar_ingrediente.php" method="POST">
        <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
        <label for="id_materia">Ingrediente:</label>
        <select name="id_materia" required>
            <?php
            $materias = $conexion->query("SELECT id_materiaPrima, nombre_materiaPrima FROM MateriaPrima");
            while ($mat = $materias->fetch_assoc()) {
                echo "<option value='{$mat['id_materiaPrima']}'>{$mat['nombre_materiaPrima']}</option>";
            }
            ?>
        </select>

        <label for="cantidad">Cantidad:</label>
        <input type="number" step="0.001" name="cantidad" required>

        <button type="submit">Agregar Ingrediente</button>
    </form>

    <!-- Tabla con ingredientes de la receta -->
    <h3>Ingredientes actuales</h3>
    <table border="1">
        <tr>
            <th>Nombre</th><th>Cantidad</th><th>Unidad</th><th>Acciones</th>
        </tr>
        <?php
        $sql = "
            SELECT r.*, m.nombre_materiaPrima, m.unidad_materiaPrima 
            FROM Receta r
            JOIN MateriaPrima m ON r.fk_id_materiaprima = m.id_materiaPrima
            WHERE r.fk_id_producto = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['nombre_materiaPrima']}</td>
                    <td>{$row['cantidad']}</td>
                    <td>{$row['unidad_materiaPrima']}</td>
                    <td>
                        <a href='eliminar_ingrediente.php?id_producto={$id_producto}&id_materia={$row['fk_id_materiaprima']}'>🗑️</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
</body>
</html>

