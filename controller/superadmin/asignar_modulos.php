<?php

include("../../conexionBD/conexion.php");

$id_restaurante = $_POST['id_restaurante'];
$modulo = $_POST['nombre_modulo'];
$accion = $_POST['accion'];

if ($accion === 'agregar') {
    $stmt = $conexion->prepare("INSERT INTO ModuloRestaurante (fk_id_restaurante, nombre_modulo) VALUES (?, ?)");
    $stmt->bind_param("is", $id_restaurante, $modulo);
    $stmt->execute();
} elseif ($accion === 'quitar') {
    $stmt = $conexion->prepare("DELETE FROM ModuloRestaurante WHERE fk_id_restaurante = ? AND nombre_modulo = ?");
    $stmt->bind_param("is", $id_restaurante, $modulo);
    $stmt->execute();
}

header("Location: ver_modulos.php?id=$id_restaurante");
?>