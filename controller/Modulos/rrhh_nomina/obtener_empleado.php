<?php
include("../../../conexionBD/conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM Empleado WHERE id_empleado = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    echo json_encode($resultado->fetch_assoc());
}
?>
