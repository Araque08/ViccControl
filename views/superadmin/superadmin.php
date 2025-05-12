<?php
// superadmin.php
session_start();

// ⏱ Tiempo límite de inactividad (en segundos)
$tiempo_limite = 1200; // 20 minutos

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


if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'SuperAdmin') {
    header("Location: ../../index.php");
    exit;
}

include("../../conexionBD/conexion.php");
$sql = "SELECT * FROM Restaurante";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel SuperAdmin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/admin.css">
</head>
<body>
<div class="header">
    <div class="titulo">
        <h1>👑 Panel SuperAdmin</h1>
    </div>

    <div class="logo">
        <img src="../../public/img/ViccControlImg.png" alt="logo de la compañia">
    </div>
</div>

<div class="menu">
    <ul>
        <li><a href="#" onclick="mostrarUsuarios()">👤 Usuarios y Roles</a></li>
        <li><a href="#" onclick="mostrarGestionRoles()">🛠 Gestionar Roles</a></li>
        <li><a href="dashboard.php">📊 Estadísticas</a></li>
        <li><a href="../../controller/cerrar_sesion.php">🚪 Cerrar sesión</a></li>   
    </ul>
</div>

<div class="container_principal">
    <h2 class="titulo_tabla">Listado de Restaurantes</h2>
    <div class="agregar">
            <a href="#" onclick="mostrarRestaurante()"><i class="fa-regular fa-square-plus"></i></a>
    </div>
    <div class="container_tabla">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Membresía</th>
                    <th>Vigencia</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                    <th>Módulos</th>
                </tr>
                <?php while($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_restaurante'] ?></td>
                    <td><?= $row['nombre_restaurante'] ?></td>
                    <td><?= $row['direccion'] ?></td>
                    <td><?= $row['tipo_membresia'] ?></td>
                    <td><?= $row['fecha_inicio_membresia'] ?> - <?= $row['fecha_fin_membresia'] ?></td>
                    <td><button onclick="mostrarEditarRestaurante(<?= $row['id_restaurante'] ?>)">✏️</button></td>
                    <td>
                        <form method="POST" action="../../controller/superadmin/estado_restaurante.php" onsubmit="return confirm('¿Deseas cambiar el estado de este restaurante?')">
                            <input type="hidden" name="id" value="<?= $row['id_restaurante'] ?>">
                            <input type="hidden" name="estado_actual" value="<?= $row['estado'] ?>">
                            <button type="submit">
                                <?= $row['estado'] === 'activo' ? '🛑 Desactivar' : '✅ Activar' ?>
                            </button>
                        </form>
                    </td>
                    <td><button onclick="mostrarModulos(<?= $row['id_restaurante'] ?>)">📦</button></td>
                </tr>
                <?php endwhile; ?>
            </table>
    </div>
</div>
<div id="usuariosModal" class="modal">
    <div class="modal-content">
        <span class="cerrar-modal">&times;</span>
        <div id="contenidoUsuarios"></div>
    </div>
</div>
<div id="rolesModal" class="modal">
    <div class="modal-content">
        <span class="cerrar-modal">&times;</span>
        <div id="contenidoRoles"></div>
    </div>
</div>
<div id="estadisticaModal" class="modal">
    <div class="modal-content">
        <span class="cerrar-modal">&times;</span>
        <div id="contenidoEstadistica"></div>
    </div>
</div>
<div id="restauranteModal" class="modal">
    <div class="modal-content">
        <span class="cerrar-modal">&times;</span>
        <div id="contenidoRestaurante"></div>
    </div>
</div>
<div id="editarRestauranteModal" class="modal">
    <div class="modal-content">
        <span class="cerrar-modal">&times;</span>
        <div id="contenidoEditarRestaurante"></div>
    </div>
</div>
<div id="verModulosModal" class="modal">
    <div class="modal-content">
        <span class="cerrar-modal">&times;</span>
        <div id="contenidoVerModulos"></div>
    </div>
</div>
<script src="../../public/js/admin.js"></script>
</body>
</html>





