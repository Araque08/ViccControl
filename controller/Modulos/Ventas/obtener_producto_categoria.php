<?php
include('../../../conexionBD/conexion.php');

if (isset($_GET['id_categoria'])) {
    $id_categoria = intval($_GET['id_categoria']);
    
    $sql = "SELECT id_producto, nombre_producto, imagen_producto, Precio_venta 
            FROM Productos 
            WHERE fk_id_categoria = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_categoria);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $productos = [];

    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }

    echo json_encode($productos);
} else {
    echo json_encode([]);
}
?>
