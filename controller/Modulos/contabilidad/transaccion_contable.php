<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha        = $_POST['fecha'] ?? null;
    $descripcion  = $_POST['descripcion'] ?? null;
    $tipo         = $_POST['tipo_transaccion'] ?? null;
    $monto        = $_POST['monto'] ?? null;
    $id_cuenta    = $_POST['fk_id_cuenta'] ?? null;
    $referencia   = $_POST['referencia'] ?? null;
    $id_restaurante = $_POST['fk_id_restaurante'] ?? null;

    if (empty($fecha) || empty($descripcion) || empty($tipo) || !is_numeric($monto) || empty($id_cuenta)) {
        header("Location: ../../../views/modules/contabilidad/transacciones.php?error=1");
        exit;
    }

    // Inicializar referencias como NULL
    $fk_compra = $fk_factura = $fk_nomina = null;

    if (strpos($referencia, 'compra_') === 0) {
        $fk_compra = intval(str_replace('compra_', '', $referencia));
    } elseif (strpos($referencia, 'factura_') === 0) {
        $fk_factura = intval(str_replace('factura_', '', $referencia));
    } elseif (strpos($referencia, 'nomina_') === 0) {
        $fk_nomina = intval(str_replace('nomina_', '', $referencia));
    }

    $sql = "INSERT INTO TransaccionContable 
            (fecha_transaccion, descripcion, tipo_transaccion, monto, fk_id_cuenta, fk_id_compra, fk_id_factura, fk_id_detalle_nomina, fk_id_restaurante)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssdiisii", 
        $fecha, 
        $descripcion, 
        $tipo, 
        $monto, 
        $id_cuenta, 
        $fk_compra, 
        $fk_factura, 
        $fk_nomina, 
        $id_restaurante
    );

    if ($stmt->execute()) {
        $conexion->query("UPDATE CuentaContable SET saldo_cuenta = saldo_cuenta + $monto WHERE id_cuenta = $id_cuenta");
        header("Location: ../../../views/modules/contabilidad/transacciones.php?success=1");
        exit;
    } else {
        echo "❌ Error al guardar la transacción: " . $stmt->error;
    }
} else {
    header("Location: ../../../views/modules/contabilidad/transacciones.php");
    exit;
}
?>


