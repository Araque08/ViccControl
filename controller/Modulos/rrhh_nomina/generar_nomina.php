<?php
session_start();
include("../../../conexionBD/conexion.php");

// Captura de datos del formulario
$periodo = $_POST['periodo']; // Ej: Mayo 2025
$fecha_pago =  $_POST['fecha_nomina'];
$salario_bruto = $_POST['salario_bruto'];
$bonificaciones = $_POST['bonificaciones'];
$deducciones_input = $_POST['deducciones'];  // si las envías manuales
$empleados = array_keys($salario_bruto);

// Paso 1: Insertar registro en NOMINA
$sql_nomina = "INSERT INTO Nomina (periodo, fecha_pago, total_pago) VALUES (?, ?, 0)";
$stmt_nomina = $conexion->prepare($sql_nomina);
$stmt_nomina->bind_param("ss", $periodo, $fecha_pago);
$stmt_nomina->execute();
$id_nomina = $stmt_nomina->insert_id;

// Paso 2: Obtener todas las deducciones (porcentaje)
$deducciones_definidas = [];
$res_d = $conexion->query("SELECT * FROM Deducciones");
while ($row = $res_d->fetch_assoc()) {
    $deducciones_definidas[] = $row;
}

// Inicializar total global
$total_global = 0;

// Paso 3: Para cada empleado...
foreach ($empleados as $id_empleado) {
    $bruto = $salario_bruto[$id_empleado];
    $bono = $bonificaciones[$id_empleado] ?? 0;

    // Deducciones seleccionadas por el usuario
    $deducciones_seleccionadas = $deducciones_input[$id_empleado] ?? [];

    $total_deducciones = 0;
    $detalles_deducciones = [];

    foreach ($deducciones_definidas as $deduccion) {
        if (in_array($deduccion['id_deducciones'], $deducciones_seleccionadas)) {
            $monto = $bruto * ($deduccion['porcentaje_deduccion'] / 100);
            $total_deducciones += $monto;
            $detalles_deducciones[] = [
                'fk_id_deduccion' => $deduccion['id_deducciones'],
                'monto' => $monto
            ];
        }
    }

    $salario_neto = $bruto + $bono - $total_deducciones;

    // Paso 4: Insertar en DetalleNomina
    $sql_detalle = "INSERT INTO DetalleNomina (salario_bruto, total_deducciones, salario_neto, fk_id_nomina, fk_id_empleado)
                    VALUES (?, ?, ?, ?, ?)";
    $stmt_det = $conexion->prepare($sql_detalle);
    $stmt_det->bind_param("dddii", $bruto, $total_deducciones, $salario_neto, $id_nomina, $id_empleado);
    $stmt_det->execute();
    $id_detalle_nomina = $stmt_det->insert_id;

    // Paso 5: Insertar en Deduccion_Nomina
    foreach ($detalles_deducciones as $ded) {
        $sql_deduccion = "INSERT INTO Deduccion_Nomina (fk_id_Deduccion, fk_id_DetalleNomina, monto)
                          VALUES (?, ?, ?)";
        $stmt_d = $conexion->prepare($sql_deduccion);
        $stmt_d->bind_param("iid", $ded['fk_id_deduccion'], $id_detalle_nomina, $ded['monto']);
        $stmt_d->execute();
    }

    // Sumar al total general
    $total_global += $salario_neto;
}


// Paso 6: Actualizar total general de nómina
$conexion->query("UPDATE Nomina SET total_pago = $total_global WHERE id_nomina = $id_nomina");

// Redirigir
header("Location: ../../../views/modules/rrhh_nomina/nomina.php?success=nomina_registrada");
exit;
