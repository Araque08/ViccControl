<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_GET['id'])) {
    echo "ID de categoría no proporcionado";
    exit;
}

$tiempo_limite = 1200;

if (isset($_SESSION['ultimo_acceso'])) {
    $inactividad = time() - $_SESSION['ultimo_acceso'];
    if ($inactividad > $tiempo_limite) {
        session_unset();
        session_destroy();
        header("Location: ../../../index.php?expirada=1");
        exit;
    }
}
$_SESSION['ultimo_acceso'] = time();

$id_categoria = intval($_GET['id']);
$id_restaurante = $_SESSION['id_restaurante'];

$sql = "SELECT * FROM CategoriaProducto 
        WHERE id_categoria = ? AND fk_id_restaurante = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_categoria, $id_restaurante);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Categoría no encontrada";
    exit;
}

$categoria = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" type="image/png" href="../../../public/favicon.png">
</head>
<body>
    <div class="container-categoria">
        <h2>Editar Categoría</h2>
        <form id="editCategoryForm" method="POST" action="../../../controller/Modulos/Ventas/añadir_categoria.php" enctype="multipart/form-data">
            <input type="hidden" name="id_categoria" value="<?= $categoria['id_categoria'] ?>">

            <div class="container-input">
                <label for="categoryName">Nombre de la Categoría:</label>
                <input type="text" id="categoryName" name="categoryName" value="<?= htmlspecialchars($categoria['nombre_categoria']) ?>" required>
            </div>

            <div class="container-input">
                <label for="categoryImage">Seleccionar imagen:</label>
                <input type="file" id="categoryImage" name="categoryImage" accept="image/*">
                <p>Imagen actual: <?= htmlspecialchars($categoria['imagen_categoria']) ?></p>
            </div>

            <button class="add-category" type="submit">Actualizar</button>
        </form>
    </div>
</body>
</html>