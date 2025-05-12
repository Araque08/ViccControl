<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

include("../../../conexionBD/conexion.php");

// ⏱ Tiempo límite de inactividad (en segundos)
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

$id_restaurante = $_SESSION['id_restaurante'];

$sql = "SELECT nombre_modulo FROM ModuloRestaurante WHERE fk_id_restaurante = ? AND estado = 'activo'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_restaurante);
$stmt->execute();
$resultado = $stmt->get_result();

$modulos_activos = [];
while ($row = $resultado->fetch_assoc()) {
    $modulos_activos[] = $row['nombre_modulo'];
}

setlocale(LC_TIME, 'es_ES.UTF-8');
$nombreRestaurante = $_SESSION['Restaurante'] ?? 'Restaurante';
$mesActual = ucfirst(strftime("%B %Y"));
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Modulo de Control</title>
    <link rel="stylesheet" href="../../../public/css/menu.css">
    <link rel="stylesheet" href="../../../public/css/compras_inventario.css">
</head>
<body>
<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div>
                <h1><strong>Materia Prima</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
<!--Formulario compras -->

    <div class="container">
        <div class="container_formulario">
            <h1>Materia Prima</h1>
            
            <!-- Formulario para crear nueva materia prima -->
            <div class="form-section">
                <h3>Crear nueva materia prima</h3>
                <form action="guardar_materia.php" method="POST">
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="text" name="unidad" placeholder="Unidad" required>
                    <!-- Categoría Dropdown o botón para agregar nueva categoría -->
                    <div>
                        <label for="categoria">Categoría:</label>
                        <select name="categoria" id="categoria" required>
                            <!-- Aquí debes agregar las categorías dinámicamente -->
                            <option value="">Seleccionar Categoría</option>
                            <option value="Nueva" id="nuevaCategoriaOption">Agregar Nueva Categoría</option>
                        </select>
                    </div>
                    <input type="number" name="stock_min" placeholder="Stock mínimo" required>
                    <textarea name="descripcion" placeholder="Descripción" rows="4" required></textarea>
                    <button type="submit">Crear</button>
                </form>
            </div>
        </div>
        <div class="container_buscar">
            <!-- Buscar por primera letra -->
            <div class="search-section">
                <h3>Buscar por nombre</h3>
                <input type="text" id="search" placeholder="Buscar...">
                <button id="searchBtn">Buscar</button>
            </div>

            <!-- Tabla con la lista de materias primas -->
            <table id="materiaPrimaTable">
                <thead>
                    <tr>
                        <th>Cod</th>
                        <th>Nombre</th>
                        <th>Unidad</th>
                        <th>Categoría</th>
                        <th>Stock min</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí se deben cargar las materias primas desde la base de datos -->
                    <tr>
                        <td>01</td>
                        <td>Pan</td>
                        <td>Unidad</td>
                        <td>Alimentos</td>
                        <td>50</td>
                        <td>Pan de trigo</td>
                        <td class="action-buttons">
                            <a href="#">✏️</a>
                            <a href="#">🗑️</a>
                        </td>
                    </tr>
                    <tr>
                        <td>02</td>
                        <td>Queso</td>
                        <td>Kilogramo</td>
                        <td>Lácteos</td>
                        <td>30</td>
                        <td>Queso fresco</td>
                        <td class="action-buttons">
                            <a href="#">✏️</a>
                            <a href="#">🗑️</a>
                        </td>
                    </tr>
                    <!-- Más filas se agregarían dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>
    <script src="../../../public/js/compras_inventario.js"></script>
</body>
</html>
