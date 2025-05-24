<?php
session_start();
include("../../../conexionBD/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar sesión de restaurante
    if (!isset($_SESSION['id_restaurante'])) {
        header("Location: /../../../views/modules/ventas/categorias.php?error=sin_sesion");
        exit;
    }

    $id_restaurante = intval($_SESSION['id_restaurante']);
    $categoryName = trim($_POST['categoryName']);
    $categoryImage = $_FILES['categoryImage'];
    $updateImage = false;
    $uploadFile = null;

    // Verificar si se subió una imagen
    if ($categoryImage && $categoryImage['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../../../public/img/ModuloVentas/Products/category/";
        $uploadFile = $uploadDir . basename($categoryImage['name']);

        if (!move_uploaded_file($categoryImage['tmp_name'], $uploadFile)) {
            header("Location: /../../../views/modules/ventas/categorias.php?error=imagen_error");
            exit;
        }

        $updateImage = true;
    }

    // Si viene id_categoria => ACTUALIZA
    if (!empty($_POST['id_categoria']) && is_numeric($_POST['id_categoria'])) {
        $id_categoria = intval($_POST['id_categoria']);

        if ($updateImage) {
            $sql = "UPDATE CategoriaProducto 
                    SET nombre_categoria = ?, imagen_categoria = ?, fk_id_restaurante = ?
                    WHERE id_categoria = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssii", $categoryName, $uploadFile, $id_restaurante, $id_categoria);
        } else {
            $sql = "UPDATE CategoriaProducto 
                    SET nombre_categoria = ?, fk_id_restaurante = ?
                    WHERE id_categoria = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sii", $categoryName, $id_restaurante, $id_categoria);
        }

        if ($stmt->execute()) {
            header("Location: /../../../views/modules/ventas/categorias.php?editado=1");
            exit;
        } else {
            header("Location: /../../../views/modules/ventas/categorias.php?error=fallo_actualizar");
            exit;
        }

    } else {
        // Si NO viene id_categoria => INSERTA NUEVA
        if (!$updateImage) {
            header("Location: /../../../views/modules/ventas/categorias.php?error=imagen_requerida");
            exit;
        }

        $sql = "INSERT INTO CategoriaProducto (nombre_categoria, imagen_categoria, fk_id_restaurante)
                VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssi", $categoryName, $uploadFile, $id_restaurante);

        if ($stmt->execute()) {
            header("Location: /../../../views/modules/ventas/categorias.php?creado=1");
            exit;
        } else {
            header("Location: /../../../views/modules/ventas/categorias.php?error=fallo_crear");
            exit;
        }
    }

    $stmt->close();
}
?>


