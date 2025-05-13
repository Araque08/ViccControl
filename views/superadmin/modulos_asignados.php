<?php
session_start();

// Validación de sesión y permisos
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'SuperAdmin') {
    header("Location: /proyecto/index.php");
    exit;
}

include("/proyecto/conexionBD/conexion.php");

$id_restaurante = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consulta nombre del restaurante
$sql_rest = "SELECT nombre_restaurante FROM Restaurante WHERE id_restaurante = ?";
$stmt_rest = $conexion->prepare($sql_rest);
$stmt_rest->bind_param("i", $id_restaurante);
$stmt_rest->execute();
$result_rest = $stmt_rest->get_result();
$restaurante = $result_rest->fetch_assoc();
$stmt_rest->close();

// Consulta módulos activos para el restaurante
$sql_modulos = "SELECT nombre_modulo, estado FROM ModuloRestaurante WHERE fk_id_restaurante = ?";
$stmt_mod = $conexion->prepare($sql_modulos);
$stmt_mod->bind_param("i", $id_restaurante);
$stmt_mod->execute();
$result_mod = $stmt_mod->get_result();

$modulos = [];
while ($row = $result_mod->fetch_assoc()) {
    $modulos[$row['nombre_modulo']] = $row['estado'];
}
$stmt_mod->close();
$conexion->close();

// Lista de todos los módulos disponibles
$lista_modulos = [
    'ventas' => 'Ventas',
    'contabilidad' => 'Contabilidad',
    'compras_inventario' => 'Compras e Inventario',
    'clientes_proveedores' => 'Clientes y Proveedores',
    'rrhh_nomina' => 'RRHH y Nómina'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulos del Restaurante</title>
    <link rel="stylesheet" href="/proyecto/public/css/admin.css">
</head>
<body>
    <h2>Módulos asignados a: <?= htmlspecialchars($restaurante['nombre_restaurante']) ?></h2>

    <div class="modulos-container">
        <?php foreach ($lista_modulos as $clave => $nombre): 
            $estado = isset($modulos[$clave]) ? $modulos[$clave] : 'inactivo'; ?>
            
            <form method="POST" action="/proyecto/controller/superadmin/estado_modulo.php" onsubmit="return confirm('¿Deseas cambiar el acceso al módulo <?= $nombre ?>?')">
                <input type="hidden" name="id_restaurante" value="<?= $id_restaurante ?>">
                <input type="hidden" name="modulo" value="<?= $clave ?>">
                <input type="hidden" name="estado_actual" value="<?= $estado ?>">
                <button type="submit">
                    <?= $estado === 'activo' ? '✅ ' : '🛑' ?><?= $nombre ?>
                </button>
            </form>

        <?php endforeach; ?>
    </div>
</body>
</html>
