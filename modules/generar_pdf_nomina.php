<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../lib/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include("../conexionBD/conexion.php");

// Validación de la fecha
$fecha = $_GET['fecha'] ?? null;
if (!$fecha) {
    die("⚠️ Falta la fecha en la URL. Usa ?fecha=2025-05-28");
}

$fecha_obj = new DateTime($fecha);
$mes = $fecha_obj->format('F');
$anio = $fecha_obj->format('Y');

// Obtener el ID de nómina y total
$sql_nomina = "SELECT id_nomina, total_pago, periodo FROM Nomina WHERE DATE(fecha_pago) = ?";
$stmt_nomina = $conexion->prepare($sql_nomina);
$stmt_nomina->bind_param("s", $fecha);
$stmt_nomina->execute();
$res_nomina = $stmt_nomina->get_result();

if ($res_nomina->num_rows === 0) {
    die("⚠️ No se encontró nómina registrada para esa fecha.");
}

$nomina_data = $res_nomina->fetch_assoc();
$id_nomina = $nomina_data['id_nomina'];
$total_general = $nomina_data['total_pago'];
$periodo = $nomina_data['periodo'];

// Obtener detalle de empleados
$sql = "SELECT dn.*, e.nombre_empleado, e.apellido_empleado, e.cedula
        FROM DetalleNomina dn
        JOIN Empleado e ON dn.fk_id_empleado = e.id_empleado
        WHERE dn.fk_id_nomina = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_nomina);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("⚠️ No se encontraron empleados para esta nómina.");
}

// Construcción del HTML
$html = '
<h2 style="text-align: center;">🧾 Reporte de Nómina - ' . $periodo . '</h2>
<p><strong>Fecha de pago:</strong> ' . $fecha . '</p>
<table style="width: 100%; border-collapse: collapse;" border="1">
<thead>
    <tr style="background-color: #e5e7eb;">
        <th>Cédula</th>
        <th>Empleado</th>
        <th>Salario Bruto</th>
        <th>Bonificaciones</th>
        <th>Deducciones</th>
        <th>Salario Neto</th>
    </tr>
</thead>
<tbody>';

while ($row = $resultado->fetch_assoc()) {
    $html .= '<tr>
        <td>' . $row['cedula'] . '</td>
        <td>' . $row['nombre_empleado'] . ' ' . $row['apellido_empleado'] . '</td>
        <td>$' . number_format($row['salario_bruto'], 2) . '</td>
        <td>$' . number_format($row['salario_neto'] - ($row['salario_bruto'] - $row['total_deducciones']), 2) . '</td>
        <td>$' . number_format($row['total_deducciones'], 2) . '</td>
        <td>$' . number_format($row['salario_neto'], 2) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$html .= '
<br><h3 style="text-align: right;">💰 Total General de la Nómina: $' . number_format($total_general, 2) . '</h3>';

// Generar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("nomina_" . $periodo . ".pdf", ["Attachment" => false]);
exit;
