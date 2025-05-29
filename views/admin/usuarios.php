<?php
session_start();
if ($_SESSION['Rol'] !== 'Administrador') {
    echo "Acceso no autorizado";
    exit;
}

include("../../conexionBD/conexion.php");

// Obtener todos los roles disponibles (excepto SuperAdmin)
$sql = "SELECT id_rol, nombre_rol FROM Rol WHERE nombre_rol != 'SuperAdmin'  AND nombre_rol != 'Administrador'";
$resultado = $conexion->query($sql);

// Obtener los empleados registrados con cédula y nombre
$sql_empleados = "SELECT id_empleado, cedula, CONCAT(nombre_empleado, ' ', apellido_empleado) AS nombre_completo 
                  FROM Empleado 
                  WHERE cedula IS NOT NULL AND cedula <> ''";
$result_empleados = $conexion->query($sql_empleados);

$id_restaurante = $_SESSION['id_restaurante'];

$sql_usuarios = "
    SELECT u.id_usuario, u.nombre_usuario, e.cedula, CONCAT(e.nombre_empleado, ' ', e.apellido_empleado) AS nombre_completo, r.nombre_rol
    FROM Usuario u
    JOIN Empleado e ON u.fk_id_empleado = e.id_empleado
    JOIN UsuarioRol ur ON u.id_usuario = ur.fk_id_usuario
    JOIN Rol r ON ur.fk_id_rol = r.id_rol
    WHERE e.fk_id_restaurante = $id_restaurante
";
$result_usuarios = $conexion->query($sql_usuarios);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Crear Usuario de Empleado</title>
    <link rel="stylesheet" href="../../public/css/usuarios_nuevos.css"> <!-- Estilo si lo deseas -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../public/favicon.png">
</head>
<body>
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="../../views/menu/main_menu.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Usuario</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">Creado Perectamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-success">Error al crearlo.</div>
    <?php endif; ?>


    <div class="form_container">
        <form action="../../controller/admin/crear_usuario.php" method="POST">
            <h2>Crear Usuario</h2>
            <label for="empleado">Seleccionar Empleado:</label>
            <select name="empleado" id="empleado" required>
                <option value="" disabled selected>Seleccione un empleado</option>
                <?php while ($emp = $result_empleados->fetch_assoc()): ?>
                    <option value="<?= $emp['id_empleado'] ?>">
                        <?= $emp['cedula'] . " - " . $emp['nombre_completo'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="text" id="usuario" name="usuario" placeholder="Usuario" required><br>
            <input type="password" id="contrasena" name="contrasena" placeholder="contraseña" required><br>

            <select name="id_rol" id="id_rol" required>
                <option value="" disabled selected>Seleccione un rol</option>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <option value="<?= $row['id_rol'] ?>"><?= $row['nombre_rol'] ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Crear Usuario</button>
        </form>
        <div class="usuario-lista" style="margin: 40px 0;">
            <h2>👥 Usuarios Registrados del Restaurante</h2>
            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f0f0f0;">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = $result_usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?= $u['id_usuario'] ?></td>
                            <td><?= $u['nombre_usuario'] ?></td>
                            <td><?= $u['cedula'] ?></td>
                            <td><?= $u['nombre_completo'] ?></td>
                            <td><?= $u['nombre_rol'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
