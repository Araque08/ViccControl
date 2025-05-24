<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../../../views/modules/ventas/ver_categoria.php?id_categoria=" . $_SESSION['id_categoria'] . "&error=falta_id");
    exit;
}

$id_producto = intval($_GET['id']);
$id_categoria = $_SESSION['id_categoria'];
$id_restaurante = $_SESSION['id_restaurante'];

// Paso 1: Eliminar recetas asociadas
$sql_recetas = "DELETE FROM Receta WHERE fk_id_producto = ? AND fk_id_restaurante = ?";
$stmt_recetas = $conexion->prepare($sql_recetas);
$stmt_recetas->bind_param("ii", $id_producto, $id_restaurante);
$stmt_recetas->execute();
$stmt_recetas->close();

// Paso 2: Eliminar el producto
$sql_producto = "DELETE FROM Productos WHERE id_producto = ? AND fk_id_categoria = ? AND fk_id_restaurante = ?";
$stmt_producto = $conexion->prepare($sql_producto);
$stmt_producto->bind_param("iii", $id_producto, $id_categoria, $id_restaurante);

if ($stmt_producto->execute()) {
    header("Location: ../../../views/modules/ventas/ver_categoria.php?id_categoria=$id_categoria&eliminado=1");
    exit;
} else {
    header("Location: ../../../views/modules/ventas/ver_categoria.php?id_categoria=$id_categoria&error=fallo_delete");
    exit;
}
?>
