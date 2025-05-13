<?php
include("../../conexionBD/conexion.php");

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$direccion = $_POST['direccion'];
$membresia = $_POST['membresia'];

$sql = "UPDATE Restaurante SET NombreRestaurante = ?, direccion = ?, tipo_membresia = ? WHERE id_restaurante = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssi", $nombre, $direccion, $membresia, $id);
$stmt->execute();

header("Location: ../../views/superadmin/superadmin.php");
?>
