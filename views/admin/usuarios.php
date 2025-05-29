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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Crear Usuario de Empleado</title>
    <link rel="stylesheet" href="../../public/css/usuarios_nuevos.css"> <!-- Estilo si lo deseas -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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
    </div>
</body>
</html>
