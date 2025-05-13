<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

include("../../../conexionBD/conexion.php");

// Verificar si los datos del formulario fueron enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $proveedor_id = $_POST['proveedor']; // ID del proveedor
    $numero_factura = $_POST['numero_factura']; // Número de factura
    $fecha_factura = $_POST['fecha_factura']; // Fecha de la factura
    $total_neto = $_POST['total_neto']; // Total Neto
    $productos = $_POST['materia_prima']; // IDs de los productos seleccionados
    $cantidades = $_POST['cantidad']; // Cantidades
    $precios = $_POST['precio_neto']; // Precios

    // Obtener el ID del restaurante desde la sesión
    $id_restaurante = $_SESSION['id_restaurante'];

    // Validar que los campos no estén vacíos
    if (empty($proveedor_id) || empty($numero_factura) || empty($fecha_factura) || empty($total_neto) || empty($productos) || empty($cantidades) || empty($precios)) {
        echo "Todos los campos son requeridos.";
        exit;
    }

    // Insertar la compra en la base de datos
    $sql_compra = "INSERT INTO Compras (id_compra, fk_id_proveedor, fecha_compra, totalcompra, fk_id_restaurante)
                   VALUES (?, ?, ?, ?, ?)";
    $stmt_compra = $conexion->prepare($sql_compra);
    $stmt_compra->bind_param("issdi",$numero_factura, $proveedor_id, $fecha_factura, $total_neto, $id_restaurante);

    // Ejecutar la inserción de la compra
    if ($stmt_compra->execute()) {
        // Obtener el ID de la compra insertada
        $compra_id = $numero_factura;

        // Insertar las líneas de la compra (materias primas)
        $sql_detalle_compra = "INSERT INTO DetalleCompra (fk_id_compra, fk_id_materia_prima, cantidad, precio)
                               VALUES (?, ?, ?, ?)";

        // Preparar y ejecutar la inserción para cada producto
        $stmt_detalle_compra = $conexion->prepare($sql_detalle_compra);
        for ($i = 0; $i < count($productos); $i++) {
            $producto_id = $productos[$i];
            $cantidad = $cantidades[$i];
            $precio = $precios[$i];

            $stmt_detalle_compra->bind_param("iiid", $compra_id, $producto_id, $cantidad, $precio);
            $stmt_detalle_compra->execute();
        }

        // Si todo salió bien
        header("../../../views/modules/compras_inventario/compras.php");
    } else {
        echo "Error al guardar la compra.";
    }

    // Cerrar las conexiones
    $stmt_compra->close();
    $stmt_detalle_compra->close();
    $conexion->close();
} else {
    echo "Método de solicitud no permitido.";
}

?>

