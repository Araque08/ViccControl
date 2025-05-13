<?php
// Inicia sesión para poder verificar el acceso
session_start();

// Verificar si el usuario está logueado (acceso autorizado)
if (!isset($_SESSION['Usuario'])) {
    header("Location: /proyecto/index.php");
    exit;
}

include("/proyecto/conexionBD/conexion.php");

// Obtener el ID de la categoría que se quiere eliminar
if (isset($_GET['id'])) {
    $id_categoria = intval($_GET['id']); // Convierte a entero el ID recibido por GET

    // Verificar si el ID es válido
    if ($id_categoria > 0) {
        // Consulta SQL para eliminar la categoría por su ID
        $sql = "DELETE FROM CategoriaProducto WHERE id_categoria = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_categoria);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Si la categoría fue eliminada correctamente, redirigir al listado de categorías
            header("Location: productos.php?mensaje=Categoria eliminada con éxito");
        } else {
            // Si hubo un error al eliminar
            echo "❌ Error al eliminar la categoría.";
        }

        // Cerrar el statement
        $stmt->close();
    } else {
        // Si el ID no es válido
        echo "❌ ID de categoría no válido.";
    }
} else {
    // Si no se pasa el ID por GET
    echo "❌ No se ha proporcionado un ID válido para la categoría.";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
