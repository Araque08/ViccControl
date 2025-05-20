<?php
include("../../../conexionBD/conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT id_producto, nombre_producto, Precio_venta, imagen_producto 
            FROM Productos WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode([]);
    }

    $stmt->close();
    $conexion->close();
}
?>
