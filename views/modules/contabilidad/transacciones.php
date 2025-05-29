<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

// ⏱ Tiempo límite de inactividad (en segundos)
$tiempo_limite = 1200;

if (isset($_SESSION['ultimo_acceso'])) {
    $inactividad = time() - $_SESSION['ultimo_acceso'];
    if ($inactividad > $tiempo_limite) {
        session_unset();
        session_destroy();
        header("Location: ../../../index.php?expirada=1");
        exit;
    }
}
$_SESSION['ultimo_acceso'] = time();

$id_restaurante = $_SESSION['id_restaurante'];

// Transacciones
$transacciones = $conexion->query("SELECT tc.*, cc.nombre_cuenta, cc.tipo_cuenta FROM TransaccionContable tc JOIN CuentaContable cc ON tc.fk_id_cuenta = cc.id_cuenta WHERE tc.fk_id_restaurante = $id_restaurante ORDER BY tc.fecha_transaccion DESC");

// Cuentas activas del restaurante
$cuentas = $conexion->query("SELECT id_cuenta, nombre_cuenta FROM CuentaContable WHERE estado = 'Activo' AND fk_id_restaurante = $id_restaurante");

// Compras pendientes
$compras = $conexion->query("SELECT c.id_compra, c.totalcompra, c.fecha_compra, p.nombre_proveedor FROM Compras c JOIN Proveedores p ON c.fk_id_proveedor = p.id_proveedor WHERE c.fk_id_restaurante = $id_restaurante AND c.id_compra NOT IN (SELECT fk_id_compra FROM TransaccionContable WHERE fk_id_compra IS NOT NULL)");

// Nómina pendiente
$nominas = $conexion->query("SELECT dn.id_detalle_nomina, e.nombre_empleado, e.apellido_empleado, dn.salario_neto FROM DetalleNomina dn JOIN Empleado e ON dn.fk_id_empleado = e.id_empleado WHERE dn.id_detalle_nomina NOT IN (SELECT fk_id_detalle_nomina FROM TransaccionContable WHERE fk_id_detalle_nomina IS NOT NULL)");

// Facturas pendientes
$facturas = $conexion->query("SELECT f.id_factura, f.valor_factura, f.fecha_hora_expedicion FROM Factura f WHERE f.id_factura NOT IN (SELECT fk_id_factura FROM TransaccionContable WHERE fk_id_factura IS NOT NULL)");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Contabilidad Completa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../public/css/transacciones.css">
    <link rel="stylesheet" href="../../../public/css/modal.css">
    <link rel="icon" type="image/png" href="../../../public/favicon.png">
</head>
<body>
<div class="container my-4">
    <div class="regresar">
        <a href="contabilidad_menu.php">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <h1>💼 Módulo de Contabilidad Completa</h1>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert-success">Categoria creada correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="alert-error">Categoria creada correctamente.</div>
    <?php endif; ?>

    <form method="POST" action="../../../controller/Modulos/contabilidad/transaccion_contable.php" class="row g-3">
        <div class="col-md-3">
            <label>Fecha</label>
            <input type="date" name="fecha" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Descripción</label>
            <input type="text" name="descripcion" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label>Monto ($)</label>
            <input type="number" step="0.01" name="monto" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Tipo de Transacción</label>
            <select name="tipo_transaccion" class="form-select" required>
                <option value="">-- Seleccionar --</option>
                <option value="Compra">Compra</option>
                <option value="Factura">Factura</option>
                <option value="Nómina">Nómina</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Cuenta Contable</label>
            <select name="fk_id_cuenta" class="form-select" required>
                <option value="">-- Seleccionar cuenta --</option>
                <?php while ($cuenta = $cuentas->fetch_assoc()): ?>
                    <option value="<?= $cuenta['id_cuenta'] ?>"><?= $cuenta['nombre_cuenta'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label>Referencia (opcional)</label>
            <select name="referencia" class="form-select">
                <option value="">-- Seleccionar transacción existente --</option>
                <?php while ($c = $compras->fetch_assoc()): ?>
                    <option value="compra_<?= $c['id_compra'] ?>">Compra: <?= $c['nombre_proveedor'] ?> - $<?= number_format($c['totalcompra'], 2) ?></option>
                <?php endwhile; ?>
                <?php while ($n = $nominas->fetch_assoc()): ?>
                    <option value="nomina_<?= $n['id_detalle_nomina'] ?>">Nómina: <?= $n['nombre_empleado'] . ' ' . $n['apellido_empleado'] ?> - $<?= number_format($n['salario_neto'], 2) ?></option>
                <?php endwhile; ?>
                <?php while ($f = $facturas->fetch_assoc()): ?>
                    <option value="factura_<?= $f['id_factura'] ?>">Factura: #<?= $f['id_factura'] ?> - $<?= number_format($f['valor_factura'], 2) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <input type="hidden" name="fk_id_restaurante" value="<?= $id_restaurante ?>">

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">Registrar Transacción</button>
        </div>
    </form>

    <hr>

    <h2 class="mt-5">📋 Transacciones Registradas</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Cuenta</th>
                <th>Tipo de Cuenta</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($t = $transacciones->fetch_assoc()): ?>
                <tr>
                    <td><?= $t['fecha_transaccion'] ?></td>
                    <td><?= $t['descripcion'] ?></td>
                    <td><?= $t['tipo_transaccion'] ?></td>
                    <td>$<?= number_format($t['monto'], 2) ?></td>
                    <td><?= $t['nombre_cuenta'] ?></td>
                    <td><?= $t['tipo_cuenta'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>


