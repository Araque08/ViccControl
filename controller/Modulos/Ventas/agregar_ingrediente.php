<?php
session_start();
if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

include("../../../conexionBD/conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ingrediente = $_POST['ingrediente'];
    $cantidad = $_POST['cantidad'];
    $id_producto = $_POST['id_producto']; // Este valor debería ser el ID del producto de la receta
    $id_restaurante = $_SESSION['id_restaurante'];

    // Insertar el ingrediente en la receta
    $sql = "INSERT INTO Receta (fk_id_producto, fk_id_materia_prima, cantidad, fk_id_restaurante) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iiis", $id_producto, $ingrediente, $cantidad, $id_restaurante);
    $stmt->execute();

    // Redireccionar o mostrar un mensaje de éxito
    header("Location: mostrar_receta.php?id_producto=" . $id_producto);
}
?>
