<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_GET['id'])) {
    echo "ID no proporcionado";
    exit;
}

$id = intval($_GET['id']);
$id_restaurante = $_SESSION['id_restaurante'];


$sql = "SELECT * FROM MateriaPrima WHERE id_materia_prima = ? AND fk_id_restaurante = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id, $id_restaurante);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Materia prima no encontrada";
    exit;
}

$materia = $result->fetch_assoc();

$sql_categorias = "SELECT id_categoria_materia, nombre_categoria_materia 
                   FROM CategoriaMateriaPrima 
                   WHERE fk_id_restaurante = ?";
$stmt_cat = $conexion->prepare($sql_categorias);
$stmt_cat->bind_param("i", $id_restaurante);
$stmt_cat->execute();
$categorias_result = $stmt_cat->get_result();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia Prima</title>
</head>
<body>
    <h3>editar materia prima</h3>
    <form action="/../../../controller/Modulos/compras_inventario/editar_materia.php" method="POST">
        <input type="hidden" name="id_materia" value="<?= $materia['id_materia_prima'] ?>">
        <input type="text" name="nombre" placeholder="Nombre" required value="<?= htmlspecialchars($materia['nombre_materia_prima']) ?>">
        <input type="text" name="unidad" placeholder="Unidad Medida" required value="<?= htmlspecialchars($materia['unidad_materia_prima']) ?>">
        
        <div>
            <select name="categoria" id="categoria" required>
                <option value="">Seleccionar Categoría</option>
                <?php
                while ($row = $categorias_result->fetch_assoc()) {
                    $selected = ($row['id_categoria_materia'] == $materia['fk_id_categoria_materia']) ? 'selected' : '';
                    echo '<option value="' . $row['id_categoria_materia'] . '" ' . $selected . '>' . htmlspecialchars($row['nombre_categoria_materia']) . '</option>';
                }
                ?>
            </select>

        </div>

        <input type="number" name="stock_min" placeholder="Stock mínimo" required step="any" value="<?= htmlspecialchars($materia['stock_min']) ?>">
        <div>
            <select name="estado" id="estado" required>
                <option value="Activo" <?= ($materia['estado'] === 'Activo') ? 'selected' : '' ?>>Activo</option>
                <option value="Inactivo" <?= ($materia['estado'] === 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        
        <textarea class="description" name="descripcion" placeholder="Descripción" rows="4"><?= htmlspecialchars($materia['descripcion_materia_prima']) ?></textarea>
        
        <div class="container-button">
            <button type="submit">Actualizar</button>
        </div>
    </form>
</body>
</html>