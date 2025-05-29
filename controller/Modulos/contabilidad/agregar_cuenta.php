<?php
session_start();

include '../../../conexionBD/conexion.php';

if (!isset($_SESSION['id_restaurante'])) {
    die("Sesión no válida. Restaurante no identificado.");
}

$id_restaurante = $_SESSION['id_restaurante'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_cuenta'];
    $codigo = $_POST['codigo_cuenta'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo_cuenta'];
    $saldo = $_POST['saldo_cuenta'];
    $estado = $_POST['estado'];

    // Validar campos obligatorios
    if (empty($nombre) || empty($tipo) || !is_numeric($saldo)) {
        die("Datos inválidos o incompletos.");
    }

    $sql = "INSERT INTO CuentaContable 
        (nombre_cuenta, codigo_cuenta, descripcion, tipo_cuenta, saldo_cuenta, estado, fk_id_restaurante)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación: " . $conexion->error);
    }

    $stmt->bind_param("ssssdsi", $nombre, $codigo, $descripcion, $tipo, $saldo, $estado, $id_restaurante);
    
    if ($stmt->execute()) {
        // ✅ Redirigir exitosamente a ajustes contables
        header("Location: ../../../views/modules/contabilidad/ajustes_contables.php?success=1");
        exit;
    } else {
        header("Location: ../../../views/modules/contabilidad/ajustes_contables.php?error=1");
    }
}
?>
