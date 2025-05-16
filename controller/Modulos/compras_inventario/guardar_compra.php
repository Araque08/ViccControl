<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

include("../../../conexionBD/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proveedor_id = $_POST['proveedor'];
    $numero_factura = $_POST['numero_factura'];
    $fecha_factura = $_POST['fecha_factura'];
    $total_neto = $_POST['total_factura'];
    $productos = $_POST['materia_prima'];
    $cantidades = $_POST['cantidad'];
    $precios = $_POST['precio_neto'];

    $id_restaurante = $_SESSION['id_restaurante'];

    if (empty($proveedor_id) || empty($numero_factura) || empty($fecha_factura) || empty($total_neto) || empty($productos) || empty($cantidades) || empty($precios)) {
        echo "Todos los campos son requeridos.";
        exit;
    }

    // 1️⃣ Verificar si ya existe la compra
    $sql_check = "SELECT id_compra FROM Compras WHERE id_compra = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $numero_factura);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows == 0) {
        // 2️⃣ Si NO existe, la insertamos en la tabla Compras
        $sql_compra = "INSERT INTO Compras (id_compra, fk_id_proveedor, fecha_compra, totalcompra, fk_id_restaurante)
                       VALUES (?, ?, ?, ?, ?)";
        $stmt_compra = $conexion->prepare($sql_compra);
        $stmt_compra->bind_param("issdi", $numero_factura, $proveedor_id, $fecha_factura, $total_neto, $id_restaurante);
        $stmt_compra->execute();
        $stmt_compra->close();
    }

    $stmt_check->close();

    // 3️⃣ Insertar los detalles de la compra (productos)
    $sql_detalle = "INSERT INTO DetalleCompra (fk_id_compra, fk_id_materia_prima, cantidad, precio)
                    VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conexion->prepare($sql_detalle);

    for ($i = 0; $i < count($productos); $i++) {
        $producto_id = $productos[$i];
        $cantidad = $cantidades[$i];
        $precio = $precios[$i];

        $stmt_detalle->bind_param("iiid", $numero_factura, $producto_id, $cantidad, $precio);
        $stmt_detalle->execute();
    }

    $stmt_detalle->close();
    $conexion->close();

    // 4️⃣ Redirigir después de guardar
    header("Location: ../../../views/modules/compras_inventario/compras.php");
    exit;

} else {
    echo "Método de solicitud no permitido.";
}
?>


