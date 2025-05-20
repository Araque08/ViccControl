<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/css/ventas.css">
    <title>Document</title>
</head>
<body>
    <div class="container-categoria">
        <h2>Agregar Nueva Categoría</h2>
        <form id="addCategoryForm" method="POST" action="../../../controller/Modulos/Ventas/añadir_categoria.php" enctype="multipart/form-data">
            <div class="container-input">
                <label for="categoryName">Nombre de la Categoría:</label>
                <input type="text" id="categoryName" name="categoryName" required>
            </div>
            <div class="container-input">
                <label for="categoryImage">Seleccionar imagen:</label>
                <input type="file" id="categoryImage" name="categoryImage" accept="image/*" required>
            </div>
            <button class="add-category" type="submit">Agregar</button>
        </form>
    </div>
</body>
</html>