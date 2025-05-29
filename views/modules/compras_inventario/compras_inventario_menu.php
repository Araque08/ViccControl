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
$mesActual = ucfirst(strftime("%B %Y"));
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Modulo de Control</title>
    <link rel="stylesheet" href="../../../public/css/menu.css">
    <link rel="icon" type="image/png" href="../../../public/favicon.png">
</head>
<body>
<div class="header-ventas">
    <div class="container-header">
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
                    <a href="../../../controller/cerrar_sesion.php">🚪 Cerrar sesión</a>
                    </div>
                    
                </div>
            </div>
            <div>
                <h1><strong><?= $nombreRestaurante ?></strong></h1>
                <p class="mes-actual"><?= $mesActual ?></p>
            </div>
        </div>
        <div class="logo">
            <img src="../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
    <nav class="nav">
        <!-- Módulos principales -->
        <div class="main-modules">
            <a href="../../admin/usuarios.php">
                <div class="module-card nav-menu">
                    <img src="../../../public/img/Modulos/Usuarios.png" alt="Usuarios">
                    <p>Contabilidad</p>
                </div>
            </a>
            <?php if (in_array('ventas', $modulos_activos)): ?>
                <a href="../ventas/ventas_menu.php">
                    <div class="module-card nav-menu">
                        <img src="../../../public/img/Modulos/ModuloVentas.png" alt="Ventas">
                        <p>Ventas</p>
                    </div>
                </a>
            <?php endif; ?>
            <?php if (in_array('contabilidad', $modulos_activos)): ?>
                <a href="../contabilidad/contabilidad_menu.php">
                    <div class="module-card nav-menu">
                        <img src="../../../public/img/Modulos/Modulocontabilidad.png" alt="Contabilidad">
                        <p>Contabilidad</p>
                    </div>
                </a>
            <?php endif; ?>
            <?php if (in_array('compras_inventario', $modulos_activos)): ?>
                <a href="../compras_inventario/compras_inventario_menu.php">
                    <div class="module-card nav-menu">
                        <img src="../../../public/img/Modulos/ModuloComprasInventario.png" alt="Compras e Inventario">
                        <p>Compras e Inventario</p>
                    </div>
                </a>
            <?php endif; ?>
            <?php if (in_array('clientes_proveedores', $modulos_activos)): ?>
                <a href="../clientes_proveedores/clientes_proveedores_menu.php">
                    <div class="module-card nav-menu">
                        <img src="../../../public/img/Modulos/ModuloClientesProveedores.png" alt="Clientes y Proveedores">
                        <p>Clientes y Proveedores</p>
                    </div>
                </a>
            <?php endif; ?>
            <?php if (in_array('rrhh_nomina', $modulos_activos)): ?>
                <a href="../rrhh_nomina/rrhh_nomina_menu.php">
                    <div class="module-card nav-menu">
                        <img src="../../../public/img/Modulos/ModuloRHNomina.png" alt="RRHH y Nómina">
                        <p>RRHH y Nómina</p>
                    </div>
                </a>
            <?php endif; ?>
        </div>
        <div class="completar-nav"></div>
    </nav>
</div>

<!-- Módulo específico -->
<div class="module-detail">
    <h2>Modulo de Compras e Inventario</h2>
    <div class="module-detail-cards">
        <div class="module-card">
            <a href="compras.php">
                <img src="../../../public/img/ModuloVentas/PuntoVenta.png" alt="Punto de venta">
                <div class="module-info">
                    <p>Compras</p>
                </div>
            </a>
        </div>
        <div class="module-card">
            <a href="inventario.php">    
                <img src="../../../public/img/ModuloVentas/Productos.png" alt="Productos">
                <div class="module-info">
                    <p>Inventario</p>
                </div>
            </a>
        </div>
        <div class="module-card">
            <a href="materia_prima.php">    
                <img src="../../../public/img/ModuloVentas/Productos.png" alt="Productos">
                <div class="module-info">
                    <p>Materia Prima</p>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2025-ViccControl</p>
</div>
<script src="../../../public/js/sesion.js"></script>
</body>
</html>