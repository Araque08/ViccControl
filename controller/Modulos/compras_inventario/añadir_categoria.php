<?php
// Conexión a la base de datos
include("../../../conexionBD/conexion.php");

// Verificar si los datos fueron enviados por el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $newCategoria = $_POST['new_categoria'];  // Nombre de la nueva categoría
    session_start();
    $restaurante_id = $_SESSION['id_restaurante'];  // ID del restaurante desde la sesión

    // Verificar si la categoría ya existe
    $sql_check = "SELECT * FROM CategoriaMateriaPrima WHERE nombre_categoria_materia = ? AND fk_id_restaurante = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("si", $newCategoria, $restaurante_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Si la categoría ya existe, retornar un error
        echo "La categoría ya existe.";
        exit;  // Terminamos el script si la categoría ya existe
    }

    // Insertar la nueva categoría en la base de datos
    $sql_insert = "INSERT INTO CategoriaMateriaPrima (nombre_categoria_materia, fk_id_restaurante) VALUES (?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);
    $stmt_insert->bind_param("si", $newCategoria, $restaurante_id);  // 's' para string, 'i' para integer

    // Ejecutamos la inserción
    if ($stmt_insert->execute()) {
        header("../../../views/modules/compras_inventario/materia_prima.php");
    } else {
        echo "Error al agregar la categoría.";
    }
}
?>


