<?php
    include("../../conexionBD/conexion.php");
    session_start();

    if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'SuperAdmin') {
        header("Location: ../../index.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre_rol = $_POST['nombre_rol'];
        $ruta = $_POST['ruta_inicio'];

        $sql = "INSERT INTO Rol (nombre_rol, ruta_inicio) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $nombre_rol, $ruta);
        $stmt->execute();
    }

    $roles = $conexion->query("SELECT * FROM Rol");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>roles</title>
    <link rel="stylesheet" href="../../public/css/admin.css">
    <link rel="icon" type="image/png" href="../../public/favicon.png">
</head>
<body>
    

    <div class="centrar">
        <h2>Gestión de Roles</h2>

        <form class="form-roles" method="POST">
            <div class="casilla">
                <label>Nombre Rol: </label>
                <input type="text" name="nombre_rol" required><br>
            </div>
            <div class="casilla">
                <label>Ruta Rol: </label>
                <input type="text" name="ruta_inicio" required><br>
            </div>
            <button class="boton" type="submit">Agregar Rol</button>
        </form>

        <h3>Roles existentes</h3>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ruta Inicio</th>
            </tr>
            <?php while($row = $roles->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_rol'] ?></td>
                <td><?= $row['nombre_rol'] ?></td>
                <td><?= $row['ruta_inicio'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
