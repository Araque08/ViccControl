<?php
// Inicia la sesión para obtener las variables necesarias
session_start();
include("/proyecto/conexionBD/conexion.php"); // Conexión a la base de datos

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibe los datos del formulario
    $categoryName = $_POST['categoryName'];
    $categoryImage = $_FILES['categoryImage'];

    // Verifica si la imagen se ha cargado correctamente
    if ($categoryImage['error'] === UPLOAD_ERR_OK) {
        // Define el directorio donde se guardará la imagen
        $uploadDir = "/proyecto/public/img/ModuloVentas/Products/category/";
        $uploadFile = $uploadDir . basename($categoryImage['name']);

        // Mueve la imagen cargada al directorio de destino
        if (move_uploaded_file($categoryImage['tmp_name'], $uploadFile)) {
            // Inserta la nueva categoría en la base de datos
            $sql = "INSERT INTO CategoriaProducto (nombre_categoria, imagen_categoria) VALUES (?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ss", $categoryName, $uploadFile);

            if ($stmt->execute()) {
                header("Location: /proyecto/views/modules/ventas/productos.php");
            } else {
                echo "Error al agregar la categoría: " . $stmt->error;
            }

            // Cierra la conexión
            $stmt->close();
        } else {
            echo "Error al cargar la imagen.";
        }
    } else {
        echo "No se cargó ninguna imagen o hubo un error al cargarla.";
    }
}
?>
