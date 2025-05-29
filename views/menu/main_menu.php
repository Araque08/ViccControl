<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../index.php");
    exit;
}

include("../../conexionBD/conexion.php");

// ⏱ Tiempo límite de inactividad (en segundos)
$tiempo_limite = 1200;

if (isset($_SESSION['ultimo_acceso'])) {
    $inactividad = time() - $_SESSION['ultimo_acceso'];
    if ($inactividad > $tiempo_limite) {
        session_unset();
        session_destroy();
        header("Location: ../../index.php?expirada=1");
        exit;
    }
}
$_SESSION['ultimo_acceso'] = time();

$id_restaurante = $_SESSION['id_restaurante'];

$sql = "SELECT nombre_modulo FROM ModuloRestaurante WHERE fk_id_restaurante = ? AND estado = 'activo'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_restaurante);
$stmt->execute();
$resultado = $stmt->get_result();

$modulos_activos = [];
while ($row = $resultado->fetch_assoc()) {
    $modulos_activos[] = $row['nombre_modulo'];
}

setlocale(LC_TIME, 'es_ES.UTF-8');
$nombreRestaurante = $_SESSION['Restaurante'] ?? 'Restaurante';
$_SESSION['nombre_restaurante'] = $nombreRestaurante;
$mesActual = ucfirst(strftime("%B %Y"));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="../../public/css/menu.css">
    <link rel="icon" type="image/png" href="../../public/favicon.png">
</head>
<body>
<div class="header">
<div class="compañia">
            <div class="perfil-container">
            <!-- 👤 Ícono clickeable -->
            <div class="sesion">
                <a class="perfil-icono" onclick="toggleDropdown()"><i class="fa-solid fa-user"></i></a>
            </div>
            

                <!-- Menú oculto -->
                <div id="perfilDropdown" class="perfil-dropdown">
                    <div>👤 <?= $_SESSION['usuario'] ?? 'Usuario' ?></div>
                    <div class="cerrar-sesion">
                    <a href="../../controller/cerrar_sesion.php">🚪 Cerrar sesión</a>
                    </div>
                    
                </div>
            </div>
            <div>
                <h1><strong><?= $nombreRestaurante ?></strong></h1>
                <p class="mes-actual"><?= $mesActual ?></p>
            </div>
        </div>
        <div class="logo">
            <img src="../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
</div>

<div class="content">
    <!-- Módulo de usuarios siempre visible -->
    <div class="module-card">
        <a href="../../views/admin/usuarios.php">
            <img src="../../public/img/Modulos/Usuarios.png" alt="Usuarios">
            <div class="module-info"><p>Usuarios</p></div>
        </a>
    </div>

    <?php if (in_array('ventas', $modulos_activos)): ?>
        <div class="module-card">
            <a href="../../views/modules/ventas/ventas_menu.php">
                <img src="../../public/img/Modulos/ModuloVentas.png" alt="Módulo de ventas">
                <div class="module-info"><p>Modulo de ventas</p></div>
            </a>
        </div>
    <?php endif; ?>

    <?php if (in_array('contabilidad', $modulos_activos)): ?>
        <div class="module-card">
            <a href="../../views/modules/contabilidad/contabilidad_menu.php">
                <img src="../../public/img/Modulos/Modulocontabilidad.png" alt="Módulo de contabilidad">
                <div class="module-info"><p>Modulo de contabilidad</p></div>
            </a>
        </div>
    <?php endif; ?>

    <?php if (in_array('compras_inventario', $modulos_activos)): ?>
        <div class="module-card">
            <a href="../../views/modules/compras_inventario/compras_inventario_menu.php">
                <img src="../../public/img/Modulos/ModuloComprasInventario.png" alt="Módulo de compras e Inventario">
                <div class="module-info"><p>Modulo de compras e Inventario</p></div>
            </a>
        </div>
    <?php endif; ?>

    <?php if (in_array('clientes_proveedores', $modulos_activos)): ?>
        <div class="module-card">
            <a href="../../views/modules/clientes_proveedores/clientes_proveedores_menu.php">
                <img src="../../public/img/Modulos/ModuloClientesProveedores.png" alt="Módulo de clientes y Proveedores">
                <div class="module-info"><p>Modulo de clientes y Proveedores</p></div>
            </a>
        </div>
    <?php endif; ?>

    <?php if (in_array('rrhh_nomina', $modulos_activos)): ?>
        <div class="module-card">
            <a href="../../views/modules/rrhh_nomina/rrhh_nomina_menu.php">
                <img src="../../public/img/Modulos/ModuloRHNomina.png" alt="Módulo de RRHH y Nómina">
                <div class="module-info"><p>Modulo de RRHH y Nómina</p></div>
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="footer">
    <p>&copy; 2025-ViccControl</p>
</div>

<script src="../../public/js/sesion.js"></script>
</body>
</html>

