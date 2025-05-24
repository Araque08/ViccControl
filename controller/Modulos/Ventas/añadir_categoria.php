<?php
session_start();
include("../../../conexionBD/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validación: asegurarse de que viene el ID
    if (!isset($_POST['id_categoria']) || !is_numeric($_POST['id_categoria'])) {
        header("Location: /../../../views/modules/ventas/categorias.php?error=id_faltante");
        exit;
    }

    $id_categoria = intval($_POST['id_categoria']);
    $categoryName = trim($_POST['categoryName']);
    $categoryImage = $_FILES['categoryImage'];
    $updateImage = false;

    // Verificar si se subió una nueva imagen
    if ($categoryImage['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../../../public/img/ModuloVentas/Products/category/";
        $uploadFile = $uploadDir . basename($categoryImage['name']);

        if (move_uploaded_file($categoryImage['tmp_name'], $uploadFile)) {
            $updateImage = true;
        } else {
            header("Location: /../../../views/modules/ventas/categorias.php?error=imagen_error");
            exit;
        }
    }

    // Actualizar categoría (con o sin imagen)
    if ($updateImage) {
        $sql = "UPDATE CategoriaProducto SET nombre_categoria = ?, imagen_categoria = ? WHERE id_categoria = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssi", $categoryName, $uploadFile, $id_categoria);
    } else {
        $sql = "UPDATE CategoriaProducto SET nombre_categoria = ? WHERE id_categoria = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $categoryName, $id_categoria);
    }

    if ($stmt->execute()) {
        header("Location: /../../../views/modules/ventas/categorias.php?editado=1");
        exit;
    } else {
        header("Location: /../../../views/modules/ventas/categorias.php?error=actualizar");
        exit;
    }

    $stmt->close();
}
?>

