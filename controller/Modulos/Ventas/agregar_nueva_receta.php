<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

$id_restaurante = $_SESSION['id_restaurante'];
$id_categoria   = $_POST['id_categoria'];
$nombre_producto = $_POST['nombre'];
$precio_venta    = $_POST['precio'];
$materias_primas = $_POST['materia_prima'];
$cantidades      = $_POST['cantidad'];

$imagen_producto = $_FILES['imagen_producto'] ?? null;
$ruta_imagen = "";

// Procesar imagen si se envía
if ($imagen_producto && $imagen_producto['error'] === UPLOAD_ERR_OK) {
    $nombre_archivo = basename($imagen_producto['name']);
    $ruta_imagen = "ModuloVentas/Products/recetas/" . $nombre_archivo;
    $uploadFile = "../../../public/img/" . $ruta_imagen;

    if (!move_uploaded_file($imagen_producto['tmp_name'], $uploadFile)) {
        echo "Error al cargar la imagen.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si es edición (ya existe producto)
    if (isset($_POST['id_producto']) && is_numeric($_POST['id_producto'])) {
        $id_producto = intval($_POST['id_producto']);

        // Actualizar producto
        if (!empty($ruta_imagen)) {
            $sql_update = "UPDATE Productos 
                           SET nombre_producto = ?, imagen_producto = ?, Precio_venta = ? 
                           WHERE id_producto = ?";
            $stmt = $conexion->prepare($sql_update);
            $stmt->bind_param("ssdi", $nombre_producto, $ruta_imagen, $precio_venta, $id_producto);
        } else {
            $sql_update = "UPDATE Productos 
                           SET nombre_producto = ?, Precio_venta = ? 
                           WHERE id_producto = ?";
            $stmt = $conexion->prepare($sql_update);
            $stmt->bind_param("sdi", $nombre_producto, $precio_venta, $id_producto);
        }

        if (!$stmt->execute()) {
            echo "Error al actualizar el producto: " . $stmt->error;
            exit;
        }
        $stmt->close();

    } else {
        // Crear nuevo producto
        $sql_insert = "INSERT INTO Productos (nombre_producto, imagen_producto, Precio_venta, fk_id_categoria, fk_id_restaurante) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql_insert);
        $stmt->bind_param("ssdii", $nombre_producto, $ruta_imagen, $precio_venta, $id_categoria, $id_restaurante);

        if ($stmt->execute()) {
            $id_producto = $stmt->insert_id;
            $stmt->close();
        } else {
            echo "Error al insertar producto: " . $stmt->error;
            exit;
        }
    }

    // Insertar ingredientes en la tabla Receta
    $sql_receta = "INSERT INTO Receta (fk_id_producto, fk_id_materia_prima, fk_id_restaurante, cantidad)
                   VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql_receta);

    for ($i = 0; $i < count($materias_primas); $i++) {
        $id_materia = intval($materias_primas[$i]);
        $cantidad = floatval($cantidades[$i]);

        $stmt->bind_param("iiid", $id_producto, $id_materia, $id_restaurante, $cantidad);
        $stmt->execute();
    }
    $stmt->close();

    $conexion->close();
    header("Location: ../../../views/modules/ventas/nueva_receta.php?id=$id_producto&exito=1");
    exit;
} else {
    echo "Acceso no autorizado";
}


