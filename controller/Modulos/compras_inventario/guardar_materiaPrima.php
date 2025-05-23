<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

include("../../../conexionBD/conexion.php");

// Verificar si el formulario fue enviado con el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $unidad = $_POST['unidad'];
    $categoria = $_POST['categoria'];
    $stock_min = $_POST['stock_min'];
    $estado = $_POST['estado'];
    $descripcion = $_POST['descripcion'];

    // Obtener el ID del restaurante desde la sesión
    $restaurante_id = $_SESSION['id_restaurante']; // Usamos el ID del restaurante desde la sesión

    // Verificar si la categoría seleccionada es "Nueva"
    if ($categoria == "Nueva") {
        // Si es nueva, recibimos el nombre de la nueva categoría
        $nueva_categoria = $_POST['nueva_categoria'];

        // Insertar la nueva categoría en la base de datos
        $sql_categoria = "INSERT INTO CategoriaMateriaPrima (nombre_categoria_materia, fk_id_restaurante) VALUES (?, ?)";
        $stmt_categoria = $conexion->prepare($sql_categoria);
        $stmt_categoria->bind_param("si", $nueva_categoria, $restaurante_id);

        if ($stmt_categoria->execute()) {
            // Si la categoría se insertó correctamente, la asignamos a la variable $categoria
            $categoria = $nueva_categoria;
        } else {
            // Si hubo un error al insertar la categoría, mostramos un mensaje
            echo "Error al agregar la categoría.";
            exit;
        }
    }

    // Insertar la materia prima en la base de datos
    $sql = "INSERT INTO MateriaPrima (nombre_materia_prima, unidad_materia_prima, fk_id_categoria_materia, stock_min, descripcion_materia_prima, fk_id_restaurante, estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssis", $nombre, $unidad, $categoria, $stock_min, $descripcion, $restaurante_id, $estado);

    if ($stmt->execute()) {
        header("Location: ../../../views/modules/compras_inventario/materia_prima.php");
    } else {
        echo "Error al agregar la materia prima.";
    }
}
?>
