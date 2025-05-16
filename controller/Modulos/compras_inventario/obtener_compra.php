<?php
include("../../../conexionBD/conexion.php");

if (isset($_GET['id'])) {
    $id_compra = intval($_GET['id']);
    
    // Obtener información general de la compra con nombre del proveedor
    $sql = "SELECT 
                c.id_compra, 
                c.fk_id_proveedor AS id_proveedor, 
                c.fecha_compra, 
                c.totalcompra AS total_neto,
                p.nombre_proveedor
            FROM Compras c 
            JOIN Proveedores p ON c.fk_id_proveedor = p.id_proveedor
            WHERE c.id_compra = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_compra);
    $stmt->execute();
    $compra = $stmt->get_result()->fetch_assoc();

    // Obtener detalles de la compra
    $sql_detalles = "SELECT 
                        mp.nombre_materia_prima AS producto, 
                        dc.cantidad, 
                        dc.precio 
                    FROM DetalleCompra dc
                    JOIN MateriaPrima mp ON dc.fk_id_materia_prima = mp.id_materia_prima
                    WHERE dc.fk_id_compra = ?";
    $stmt = $conexion->prepare($sql_detalles);
    $stmt->bind_param("i", $id_compra);
    $stmt->execute();
    $result_detalles = $stmt->get_result();

    $detalles = [];
    while ($row = $result_detalles->fetch_assoc()) {
        $row['num_docu'] = $compra['id_compra']; // Agregar número de factura a cada fila
        $row['fecha'] = $compra['fecha_compra']; // Agregar fecha a cada fila
        $row['proveedor'] = $compra['nombre_proveedor']; // Agregar proveedor a cada fila
        $detalles[] = $row;
    }

    // Combinar todo en un solo JSON
    echo json_encode([
        "id_proveedor" => $compra['id_proveedor'],
        "numero_factura" => $compra['id_compra'],
        "fecha_factura" => $compra['fecha_compra'],
        "total_neto" => $compra['total_neto'],
        "nombre_proveedor" => $compra['nombre_proveedor'],
        "detalles" => $detalles
    ]);
}
?>
