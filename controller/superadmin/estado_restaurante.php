<?php
session_start();
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'SuperAdmin') {
    echo "Acceso denegado";
    exit;
}

include("/proyecto/conexionBD/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['estado_actual'])) {
    $id_restaurante = $_POST['id'];
    $estado_actual = $_POST['estado_actual'];
    $nuevo_estado = $estado_actual === 'activo' ? 'inactivo' : 'activo';

    $sql = "UPDATE Restaurante SET estado = ? WHERE id_restaurante = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $nuevo_estado, $id_restaurante);
    $stmt->execute();

    header("Location: /proyecto/views/superadmin/superadmin.php");
    exit;
} else {
    echo "Solicitud inválida.";
}
?>


