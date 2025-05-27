<?php
// Conexión a base de datos
include '../conexionBD/conexion.php';

$idVenta = $_GET['id_venta'];

// Consulta de datos principales
$sql = "SELECT v.id_venta, c.nombre_cliente, c.cedula, c.telefono_cliente, 
               c.direccion_cliente, c.email_cliente, v.fecha, v.total_venta,
               mp.nombre_medio_pago
        FROM Venta v
        JOIN Cliente c ON v.fk_id_cliente = c.id_cliente
        JOIN Venta_MedioPago vp ON v.id_venta = vp.fk_id_venta
        JOIN MedioPago mp ON vp.fk_id_medio_pago = mp.id_medio_pago
        WHERE v.id_venta = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idVenta);
$stmt->execute();
$resultado = $stmt->get_result();
$venta = $resultado->fetch_assoc();

// Consulta de productos relacionados con la venta (simulado aquí)
$sqlProductos = "SELECT p.nombre_producto, dv.cantidad, dv.subtotal
                 FROM DetalleVenta dv
                 JOIN Productos p ON dv.fk_id_producto = p.id_producto
                 WHERE dv.fk_id_venta = ?";
$stmtProd = $conexion->prepare($sqlProductos);
$stmtProd->bind_param("i", $idVenta);
$stmtProd->execute();
$productos = $stmtProd->get_result();

// Calcular subtotal e impuestos (asumiendo 8% ya incluido)
$subtotal = $venta['total_venta'] / 1.08;
$igv = $venta['total_venta'] - $subtotal;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura de Venta</title>
  <style>
    body { font-family: 'Arial', sans-serif; font-size: 12px; width: 80mm; margin: 0 auto; color: #000; }
    .ticket-container { padding: 10px; }
    .centered { text-align: center; }
    .empresa-info, .cliente-info, .totales { margin-bottom: 10px; }
    .empresa-info strong, .cliente-info strong { display: block; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border-bottom: 1px dashed #999; padding: 4px 0; text-align: left; }
    th { font-weight: bold; }
    .totales p { margin: 3px 0; text-align: right; }
    .footer { margin-top: 10px; text-align: center; font-size: 10px; border-top: 1px dashed #ccc; padding-top: 5px; }
    .print-button { display: block; width: 100%; margin-top: 15px; padding: 6px; background-color: #000; color: #fff; border: none; cursor: pointer; font-size: 12px; }
    @media print { .print-button { display: none; } }
  </style>
</head>
<body>
  <div class="ticket-container">
    <div class="centered empresa-info">
      <strong>carteldelsushi</strong>
      NIT: 901.999.888-1<br>
      Calle 100 # 20-30, Bogotá<br>
      Tel: (601) 1234567
    </div>
    <hr>
    <div class="cliente-info">
      <strong>Cliente:</strong> <?= htmlspecialchars($venta['nombre_cliente']) ?><br>
      <strong>Cédula:</strong> <?= htmlspecialchars($venta['cedula']) ?><br>
      <strong>Teléfono:</strong> <?= htmlspecialchars($venta['telefono_cliente']) ?><br>
      <strong>Dirección:</strong> <?= htmlspecialchars($venta['direccion_cliente']) ?><br>
      <strong>Email:</strong> <?= htmlspecialchars($venta['email_cliente']) ?><br>
      <strong>Medio de Pago:</strong> <?= htmlspecialchars($venta['nombre_medio_pago']) ?><br>
      <strong>Fecha:</strong> <?= htmlspecialchars($venta['fecha']) ?>
    </div>
    <table>
      <thead>
        <tr><th>Producto</th><th>Cant</th><th>Total</th></tr>
      </thead>
      <tbody>
        <?php while ($row = $productos->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
            <td><?= htmlspecialchars($row['cantidad']) ?></td>
            <td>$<?= number_format($row['subtotal'], 0, ',', '.') ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <div class="totales">
      <p><strong>Subtotal:</strong> $<?= number_format($subtotal, 0, ',', '.') ?></p>
      <p><strong>IPC (8%):</strong> $<?= number_format($igv, 0, ',', '.') ?></p>
      <p><strong>Total:</strong> <strong>$<?= number_format($venta['total_venta'], 0, ',', '.') ?></strong></p>
    </div>
    <button class="print-button" onclick="window.print()">🖨️ Imprimir Factura</button>
    <div class="footer">¡Gracias por su compra!<br>Factura generada automáticamente por ViccControl.</div>
  </div>
</body>
</html>
