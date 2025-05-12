<?php
// Mostrar los ingredientes de la receta para el producto
include("../../../conexionBD/conexion.php");

$id_producto = $_GET['id_producto'];

$sql_receta = "SELECT mp.nombre_materia_prima, r.cantidad, r.precio_unitario 
        FROM Receta r
        JOIN MateriaPrima mp ON r.fk_id_materia_prima = mp.id_materia_prima
        WHERE r.fk_id_producto = ?";
$stmt = $conexion->prepare($sql_receta);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado_receta = $stmt->get_result();

// Obtener los ingredientes de la base de datos
include("../../../conexionBD/conexion.php");
$sql_ingredientes = "SELECT * FROM MateriaPrima WHERE fk_id_restaurante = ?";
$stmt = $conexion->prepare($sql_ingredientes);
$stmt->bind_param("i", $_SESSION['id_restaurante']);
$stmt->execute();
$materia_prima = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="form-section">
    <h3>Ingredientes de la receta</h3>
    <form action="agregar_ingrediente.php" method="POST">
        <!-- Nombre de Ingrediente -->
        <div>
            <label for="ingrediente">Ingrediente:</label>
            <select name="ingrediente" id="ingrediente" required>
                <option value="">Seleccionar Ingrediente</option>
                <?php

                while ($row = $materia_prima ->fetch_assoc()) {
                    echo "<option value='" . $row['id_materia_prima'] . "'>" . $row['nombre_materia_prima'] . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Cantidad de Ingrediente -->
        <div>
            <label for="cantidad">Cantidad:</label>
            <input type="number" name="cantidad" id="cantidad" step="0.01" required>
        </div>

        <!-- Enviar Formulario -->
        <button type="submit">Agregar Ingrediente</button>
    </form>
</div>
<div class="container-tabla">
    <table id="materiaPrimaTable">
        <thead>
            <tr>
                <th>Cod</th>
                <th>Nombre de Ingrediente</th>
                <th>Cantidad</th>
                <th>Costo en Receta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_materia_prima']; ?></td>
                    <td><?php echo $row['nombre_materia_prima']; ?></td>
                    <td><?php echo $row['cantidad']; ?></td>
                    <td><?php echo '$' . number_format($row['cantidad'] * $row['precio_unitario'], 2); ?></td>
                    <td>
                        <a href="#">✏️</a> <!-- Editar -->
                        <a href="#">🗑️</a> <!-- Eliminar -->
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>