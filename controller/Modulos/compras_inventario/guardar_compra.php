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
        // 2️⃣ Insertar en Compras si no existe
        $sql_compra = "INSERT INTO Compras (id_compra, fk_id_proveedor, fecha_compra, totalcompra, fk_id_restaurante)
                       VALUES (?, ?, ?, ?, ?)";
        $stmt_compra = $conexion->prepare($sql_compra);
        $stmt_compra->bind_param("issdi", $numero_factura, $proveedor_id, $fecha_factura, $total_neto, $id_restaurante);
        $stmt_compra->execute();
        $stmt_compra->close();
    }
    $stmt_check->close();

    // 3️⃣ Insertar detalle compra
    $sql_detalle = "INSERT INTO DetalleCompra (fk_id_compra, fk_id_materia_prima, cantidad, precio, subtotal)
                    VALUES (?, ?, ?, ?, ?)";
    $stmt_detalle = $conexion->prepare($sql_detalle);

    // 4️⃣ Actualizar stock
    $sql_update_stock = "UPDATE MateriaPrima SET stock_disp = stock_disp + ? WHERE id_materia_prima = ?";
    $stmt_stock = $conexion->prepare($sql_update_stock);

    // 5️⃣ Verificar existencia y actualizar/inserta en PrecioPromedio
    $sql_check_prom = "SELECT id FROM PrecioPromedio WHERE fk_id_materia_prima = ?";
    $stmt_check_prom = $conexion->prepare($sql_check_prom);

    $sql_update_prom = "UPDATE PrecioPromedio SET precio_promedio = ?, ultima_actualizacion = NOW() WHERE fk_id_materia_prima = ?";
    $stmt_update_prom = $conexion->prepare($sql_update_prom);

    $sql_insert_prom = "INSERT INTO PrecioPromedio (fk_id_materia_prima, precio_promedio, ultima_actualizacion)
                        VALUES (?, ?, NOW())";
    $stmt_insert_prom = $conexion->prepare($sql_insert_prom);

    for ($i = 0; $i < count($productos); $i++) {
        $producto_id = $productos[$i];
        $cantidad = $cantidades[$i];
        $precio = $precios[$i];
        $subtotal = $precio / $cantidad;

        // Guardar detalle
        $stmt_detalle->bind_param("iiidd", $numero_factura, $producto_id, $cantidad, $precio, $subtotal);
        $stmt_detalle->execute();

        // Actualizar stock
        $stmt_stock->bind_param("ii", $cantidad, $producto_id);
        $stmt_stock->execute();

        // Verificar si ya hay precio promedio
        $stmt_check_prom->bind_param("i", $producto_id);
        $stmt_check_prom->execute();
        $stmt_check_prom->store_result();

        if ($stmt_check_prom->num_rows > 0) {
            // Actualizar
            $precio_promedio = round($subtotal, 2);
            $stmt_update_prom->bind_param("di", $precio_promedio, $producto_id);
            $stmt_update_prom->execute();
        } else {
            // Insertar
            $precio_promedio = round($subtotal, 2);
            $stmt_insert_prom->bind_param("id", $producto_id, $precio_promedio);
            $stmt_insert_prom->execute();
        }
    }

    $stmt_detalle->close();
    $stmt_stock->close();
    $stmt_check_prom->close();
    $stmt_update_prom->close();
    $stmt_insert_prom->close();
    $conexion->close();

    header("Location: ../../../views/modules/compras_inventario/compras.php");
    exit;
} else {
    echo "Método de solicitud no permitido.";
}


