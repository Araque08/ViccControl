<?php
session_start();
include("../../../conexionBD/conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recibir variables del formulario
    $id_materia = intval($_POST['id_materia']);
    $nombre = trim($_POST['nombre']);
    $unidad = trim($_POST['unidad']);
    $categoria = intval($_POST['categoria']);
    $stock_min = intval($_POST['stock_min']);
    $descripcion = trim($_POST['descripcion']);
    $estado = trim($_POST['estado']);
    $id_restaurante = $_SESSION['id_restaurante'];

    // Validación básica
    if ($id_materia && $nombre && $unidad && $categoria && $id_restaurante) {

        // Preparar y ejecutar la consulta
        $sql = "UPDATE MateriaPrima SET 
                    nombre_materia_prima = ?, 
                    unidad_materia_prima = ?, 
                    fk_id_categoria_materia = ?, 
                    stock_min = ?, 
                    descripcion_materia_prima = ?,
                    estado = ?
                WHERE id_materia_prima = ? AND fk_id_restaurante = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssiissii", $nombre, $unidad, $categoria, $stock_min, $descripcion, $estado, $id_materia, $id_restaurante);


        if ($stmt->execute()) {
            // Redirige con éxito
            header("Location: ../../../views/modules/compras_inventario/materia_prima.php?editado=1");
            exit;
        } else {
            // Redirige con error de ejecución
            header("Location: ../../../views/modules/compras_inventario//materia_prima.php?error=1");
            exit;
        }
    } else {
        // Redirige con error de validación
        header("Location: ../../../views/modules/compras_inventario/materia_prima.php?error=campos");
        exit;
    }
}
?>
