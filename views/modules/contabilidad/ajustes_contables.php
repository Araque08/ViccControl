<?php
session_start();
include '../../../conexionBD/conexion.php';

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

$id_restaurante = $_SESSION['id_restaurante']; // asegúrate de tenerlo al iniciar sesión

// Obtener lista de cuentas
$cuentas = $conexion->query("SELECT * FROM CuentaContable ORDER BY tipo_cuenta, nombre_cuenta");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Ajustes Contables</title>
    <link rel="stylesheet" href="../../../public/css/ajustes.css">
    <link rel="stylesheet" href="../../../public/css/modal.css">
</head>
<body>
<div class="container">
    <div class="regresar">
        <a href="contabilidad_menu.php">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
    <h1>⚙️ Ajustes Contables</h1>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert-success">Categoria creada correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="alert-error">Categoria creada correctamente.</div>
    <?php endif; ?>

<form action="../../../controller/Modulos/contabilidad/agregar_cuenta.php"  method="POST" class="formulario">
    <label>Nombre de la Cuenta:</label>
    <input type="text" name="nombre_cuenta" required>

    <label>Código de Cuenta:</label>
    <input type="text" name="codigo_cuenta" placeholder="Ej: 1.1.01">

    <label>Descripción:</label>
    <textarea name="descripcion" rows="3" placeholder="Ej: Caja menor del restaurante central"></textarea>

    <label>Tipo de Cuenta:</label>
    <select name="tipo_cuenta" required>
        <option value="">-- Seleccionar --</option>
        <option value="Activo">Activo</option>
        <option value="Pasivo">Pasivo</option>
        <option value="Patrimonio">Patrimonio</option>
        <option value="Ingreso">Ingreso</option>
        <option value="Gasto">Gasto</option>
    </select>

    <label>Saldo Inicial:</label>
    <input type="number" step="0.01" name="saldo_cuenta" required>

    <label>Estado:</label>
    <select name="estado">
        <option value="Activo">Activo</option>
        <option value="Inactivo">Inactivo</option>
    </select>

    <button type="submit">Agregar Cuenta</button>
</form>


    <h2>📘 Cuentas Contables Existentes</h2>
    <table class="tabla">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Saldo</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($cuenta = $cuentas->fetch_assoc()): ?>
            <tr>
                <td><?= $cuenta['nombre_cuenta'] ?></td>
                <td><?= $cuenta['tipo_cuenta'] ?></td>
                <td>$<?= number_format($cuenta['saldo_cuenta'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
