<?php
include("../../conexionBD/conexion.php");
session_start();

if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'SuperAdmin') {
    header("Location: ../../index.php");
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM Restaurante WHERE id_restaurante = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$restaurante = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>editar restaurante</title>
    <link rel="stylesheet" href="../../public/css/admin.css">
    <link rel="icon" type="image/png" href="../../public/favicon.png">
</head>
<body>
    <div class="centrar">
        <h1>Editar Restaurante</h1> 
        <form method="POST" action="../../controller/superadmin/actualizar_restaurante.php">
            <input type="hidden" name="id" value="<?= $restaurante['id_restaurante'] ?>">
            <div class="casilla">
                <label>Nombre: </label>
                <input type="text" name="nombre" value="<?= $restaurante['nombre_restaurante'] ?>"><br>
            </div>
            <div class="casilla">
                <label>Direccion: </label>
                <input type="text" name="direccion" value="<?= $restaurante['direccion'] ?>"><br>
            </div>
            <div class="casilla">
                <label>Membresia: </label>
                <input type="text" name="membresia" value="<?= $restaurante['tipo_membresia'] ?>"><br>
            </div>
            <button class="boton" type="submit">Actualizar</button>
        </form>
    </div>
</body>
</html>
