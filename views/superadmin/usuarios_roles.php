<?php
include("/proyecto/conexionBD/conexion.php");
session_start();

if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'SuperAdmin') {
    header("Location: /proyecto/index.php");
    exit;
}

$sql = "SELECT u.id_usuario, u.nombre_usuario, r.nombre_rol, res.nombre_restaurante
        FROM Usuario u
        JOIN UsuarioRol ur ON u.id_usuario = ur.fk_id_usuario
        JOIN Rol r ON ur.fk_id_rol = r.id_rol
        JOIN Empleado e ON u.fk_id_empleado = e.id_empleado
        JOIN Restaurante res ON e.fk_id_restaurante = res.id_restaurante";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link rel="stylesheet" href="/proyecto/public/css/admin.css">
</head>
<body>

<div class="centrar">
    <h2>Usuarios y Roles</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Restaurante</th>
        </tr>
        <?php while($row = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_usuario'] ?></td>
            <td><?= $row['nombre_usuario'] ?></td>
            <td><?= $row['nombre_rol'] ?></td>
            <td><?= $row['nombre_restaurante'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>


