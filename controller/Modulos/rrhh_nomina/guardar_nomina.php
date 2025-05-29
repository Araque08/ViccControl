<?php
include("../../conexionBD/conexion.php");

$mes = $_POST['mes_pago'];
$anio = $_POST['año_pago'];
$salarios = $_POST['salario_base'];
$bonos = $_POST['bonificaciones'];
$descuentos = $_POST['deducciones'];

foreach ($salarios as $id => $salario_base) {
    $bono = $bonos[$id] ?? 0;
    $descuento = $descuentos[$id] ?? 0;
    $total = $salario_base + $bono - $descuento;

    $sql = "INSERT INTO Nomina (fk_id_empleado, mes_pago, año_pago, salario_base, bonificaciones, deducciones, total_pago)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issdddd", $id, $mes, $anio, $salario_base, $bono, $descuento, $total);
    $stmt->execute();
}

header("Location: nomina.php?success=Nómina registrada");
exit;
