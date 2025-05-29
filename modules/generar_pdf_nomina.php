<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="GET" action="generar_pdf_nomina.php">
    <label>Seleccionar Fecha:</label>
    <input type="date" name="fecha" required>
    <button type="submit">Generar PDF</button>
    </form>

</body>
</html>


<?php
require_once '../lib/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
include("../conexionBD/conexion.php");

// Obtener fecha seleccionada
$fecha = $_GET['fecha'] ?? null;
if (!$fecha) {
    die("⚠️ Fecha no especificada.");
}

// Obtener mes y año
$fecha_obj = new DateTime($fecha);
$mes = $fecha_obj->format('F');
$anio = $fecha_obj->format('Y');

// Consulta
$sql = "SELECT dn.*, e.nombre_empleado, e.apellido_empleado, e.cedula
        FROM DetalleNomina dn
        JOIN Empleado e ON dn.fk_id_empleado = e.id_empleado
        JOIN Nomina n ON dn.fk_id_nomina = n.id_nomina
        WHERE DATE(n.fecha_pago) = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $fecha);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("⚠️ No se encontró información de nómina para la fecha seleccionada.");
}

// Generar HTML para el PDF
$html = '
<h2 style="text-align: center;">🧾 Nómina del ' . $mes . ' de ' . $anio . '</h2>
<table style="width: 100%; border-collapse: collapse;" border="1">
<thead>
    <tr style="background-color: #e5e7eb;">
        <th>Cédula</th>
        <th>Empleado</th>
        <th>Salario Bruto</th>
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
        <td>$' . number_format($row['total_deducciones'], 2) . '</td>
        <td>$' . number_format($row['salario_neto'], 2) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Crear PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Mostrar PDF en navegador
$dompdf->stream("nomina_" . $mes . "_" . $anio . ".pdf", ["Attachment" => false]);
exit;


