<?php
// Conexión a la base de datos
include("../../../conexionBD/conexion.php");

if (isset($_GET['id'])) {
    $compra_id = $_GET['id'];

    // Consulta para obtener los datos de la compra seleccionada
    $sql_compra = "SELECT c.id_compra, c.fk_id_proveedor, c.fecha_compra, c.totalcompra, p.nombre_proveedor 
                   FROM Compras c
                   JOIN Proveedores p ON c.fk_id_proveedor = p.id_proveedor
                   WHERE c.id_compra = ?";
    $stmt = $conexion->prepare($sql_compra);
    $stmt->bind_param("i", $compra_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $compra = $result->fetch_assoc();
        echo json_encode([
            'numero_factura' => $compra['id_compra'],
            'fecha_factura' => $compra['fecha_compra'],
            'total_neto' => $compra['totalcompra']
        ]);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$conexion->close();
?>
