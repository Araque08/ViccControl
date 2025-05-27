<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

include("../../../conexionBD/conexion.php");

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

$query = "
SELECT 
    v.id_venta AS cod_venta,
    c.cedula AS cedula_cliente,
    e.nombre_empleado AS cajero,
    mp.nombre_medio_pago AS medio_pago,
    v.fecha,
    v.total_venta AS total
FROM Venta v
JOIN Cliente c ON v.fk_id_cliente = c.id_cliente
JOIN Empleado e ON v.fk_id_empleado = e.id_empleado
JOIN Venta_MedioPago vmp ON v.id_venta = vmp.fk_id_venta
JOIN MedioPago mp ON vmp.fk_id_medio_pago = mp.id_medio_pago
";

$resultado = $conexion->query($query);

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Modulo de Control</title>
    <link rel="stylesheet" href="../../../public/css/menu.css">
    <link rel="stylesheet" href="../../../public/css/compras_inventario.css">
    <link rel="stylesheet" href="../../../public/css/modal.css">
</head>
<body>
<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="clientes_proveedores_menu.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Clientes</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
    <div class="container">
        
        <div class="container_buscar">
            <!-- Buscar por primera letra -->
            <div class="search-section">
                <h3>Buscar por nombre</h3>
                <input type="text" id="search" placeholder="Buscar...">
                <button id="searchBtn">Buscar</button>
            </div>

            <!-- Tabla con la lista de materias primas -->
            <table id="materiaPrimaTable">
                <thead>
                    <tr>
                        <th>Cod venta</th>
                        <th>Cedula</th>
                        <th>Cajero</th>
                        <th>Medio de Pago</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['cod_venta'] ?></td>
                        <td><?= $row['cedula_cliente'] ?></td>
                        <td><?= $row['cajero'] ?></td>
                        <td><?= $row['medio_pago'] ?></td>
                        <td><?= $row['fecha'] ?></td>
                        <td>$<?= number_format($row['total'], 2) ?></td>
                        <td>
                        <button class="ver-detalle" data-id="<?= $row['cod_venta'] ?>">🧾 Ver</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>  
    </div>
    <div id="nuevoModal" class="modal">
        <div class="modal-content">
            <span class="cerrar-modal">&times;</span>
            <div id="contenidoModal"></div>
        </div>
    </div>

    <script src="/../../../public/js/editar_proveedor.js"></script>
</body>
</html>