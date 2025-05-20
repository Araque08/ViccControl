<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

$id_restaurante = $_SESSION['id_restaurante'];
$stocks = $_POST['stock_bodega'];
$fecha_inventario = $_POST['fecha_inventario'];  // 📅 Obtenemos la fecha seleccionada

foreach ($stocks as $id_materia => $stock_bodega) {
    // 1️⃣ Obtener stock disponible
    $sql_disp = "SELECT stock_disp FROM MateriaPrima WHERE id_materia_prima = ?";
    $stmt_disp = $conexion->prepare($sql_disp);
    $stmt_disp->bind_param("i", $id_materia);
    $stmt_disp->execute();
    $res = $stmt_disp->get_result()->fetch_assoc();
    $stock_disp = $res['stock_disp'];
    $diferencia = $stock_bodega - $stock_disp;

    // 2️⃣ Verificar si ya existe inventario para esa fecha y producto
    $sql_check = "SELECT id_inventario FROM Inventario 
                WHERE fk_id_materia_prima = ? AND fk_id_restaurante = ? AND fecha_inventario = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("iis", $id_materia, $id_restaurante, $fecha_inventario);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // 3️⃣ Actualizar si existe
        $sql_update = "UPDATE Inventario 
                    SET stock_bodega = ?, diferencia_stock = ? 
                    WHERE fk_id_materia_prima = ? AND fk_id_restaurante = ? AND fecha_inventario = ?";
        $stmt_upd = $conexion->prepare($sql_update);
        $stmt_upd->bind_param("iiiss", $stock_bodega, $diferencia, $id_materia, $id_restaurante, $fecha_inventario);
        $stmt_upd->execute();
    } else {
        // 4️⃣ Insertar si no existe
        $sql_insert = "INSERT INTO Inventario (fk_id_materia_prima, fk_id_restaurante, stock_bodega, diferencia_stock, fecha_inventario)
                    VALUES (?, ?, ?, ?, ?)";
        $stmt_ins = $conexion->prepare($sql_insert);
        $stmt_ins->bind_param("iiiss", $id_materia, $id_restaurante, $stock_bodega, $diferencia, $fecha_inventario);
        $stmt_ins->execute();
    }
}

// ✅ Redireccionar después de guardar
header("Location: /../../../views/modules/compras_inventario/inventario.php?success=1");
exit;
?>

