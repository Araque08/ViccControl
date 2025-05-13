<?php
session_start();
if ($_SESSION['rol'] !== 'Administrador') {
    echo "Acceso no autorizado";
    exit;
}

include("/proyecto/conexionBD/conexion.php");

// Obtener todos los roles disponibles (excepto SuperAdmin)
$sql = "SELECT id_rol, nombre_rol FROM rol WHERE nombre_rol != 'SuperAdmin'";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario de Empleado</title>
    <link rel="stylesheet" href="/proyecto/public/css/form.css"> <!-- Estilo si lo deseas -->
</head>
<body>
    <h2>➕ Crear Usuario para Empleado</h2>

    <form action="/proyecto/controller/admin/crear_usuario.php" method="POST">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="apellido">Apellido:</label><br>
        <input type="text" id="apellido" name="apellido" required><br>

        <label for="usuario">Usuario:</label><br>
        <input type="text" id="usuario" name="usuario" required><br>

        <label for="contrasena">Contraseña:</label><br>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <label for="id_rol">Rol del Usuario:</label><br>
        <select name="id_rol" id="id_rol" required>
            <option value="" disabled selected>Seleccione un rol</option>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <option value="<?= $row['id_rol'] ?>"><?= $row['nombre_rol'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Crear Usuario</button>
    </form>
</body>
</html>
