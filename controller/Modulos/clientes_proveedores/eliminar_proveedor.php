<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_GET['id'])) {
    header("Location: clientes_proveedores_menu.php?error=1");
    exit;
}

$id_proveedor = intval($_GET['id']);
$id_restaurante = $_SESSION['id_restaurante'];

// Verificar si tiene compras asociadas
$sql_check = "SELECT COUNT(*) as total FROM Compras WHERE fk_id_proveedor = ? AND fk_id_restaurante = ?";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->bind_param("ii", $id_proveedor, $id_restaurante);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();

if ($row_check['total'] > 0) {
    // Tiene compras, no se puede borrar
    header("Location: ../../../views/modules/clientes_proveedores/proveedores.php?error=compras_asociadas");
    exit;
}

// Si no tiene compras, eliminar
$sql_delete = "DELETE FROM Proveedores WHERE id_proveedor = ? AND fk_id_restaurante = ?";
$stmt_delete = $conexion->prepare($sql_delete);
$stmt_delete->bind_param("ii", $id_proveedor, $id_restaurante);

if ($stmt_delete->execute()) {
    header("Location: ../../../views/modules/clientes_proveedores/proveedores.php?eliminado=1");
    exit;
} else {
    header("Location: ../../../views/modules/clientes_proveedores/proveedores.php?error=error_eliminar");
    exit;
}
?>

