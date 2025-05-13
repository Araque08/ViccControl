<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: /../../../index.php");
    exit;
}

include("/proyecto/conexionBD/conexion.php");

// ⏱ Tiempo límite de inactividad (en segundos)
$tiempo_limite = 1200;

if (isset($_SESSION['ultimo_acceso'])) {
    $inactividad = time() - $_SESSION['ultimo_acceso'];
    if ($inactividad > $tiempo_limite) {
        session_unset();
        session_destroy();
        header("Location: /proyecto/index.php?expirada=1");
        exit;
    }
}
$_SESSION['ultimo_acceso'] = time();

$id_restaurante = $_SESSION['id_restaurante'];

$sql = "SELECT * FROM CategoriaProducto";
$result = $conexion->query($sql);

setlocale(LC_TIME, 'es_ES.UTF-8');
$nombreRestaurante = $_SESSION['Restaurante'] ?? 'Restaurante';
$mesActual = ucfirst(strftime("%B %Y"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Productos</title>
    <link rel="stylesheet" href="/proyecto/public/css/menu.css">
    <link rel="stylesheet" href="/proyecto/public/css/ventas.css">
    <link rel="stylesheet" href="/proyecto/public/css/modal.css">
</head>
<body>
<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="ventas_menu.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Productos</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="/proyecto/public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
</div>
<div class="productos">

    <div class="container-productos">

        <?php
            while ($row = $result->fetch_assoc()) {
                echo '<div class="products-container">
                        <a href="ver_categoria.php?id_categoria=' . $row['id_categoria'] . '">
                            <div class="product">
                                <img src="' . $row['imagen_categoria'] . '" alt="' . $row['nombre_categoria'] . '">
                                <p>' . $row['nombre_categoria'] . '</p>
                                <div class="menu-dots">
                                    <i class="fa-solid fa-ellipsis-vertical" onclick="toggleMenu(' . $row['id_categoria'] . ')"></i>
                                        <div id="menu-' . $row['id_categoria'] . '" class="options-menu">
                                            <a href="editar_categoria.php?id=' . $row['id_categoria'] . '">Editar</a>
                                            <a href="/proyecto/controller/Modulos/Ventas/eliminar_categoria.php?id=' . $row['id_categoria'] . '">Eliminar</a>
                                        </div>
                                </div>
                            </div>
                        </a>
                    </div>';
            }
        ?>

        <a href="nueva_receta.php">Hola Mundo</a>

        <div class="products-container" id="products-container">
            <a href="#" onclick="mostrarCategoria()">
                <div class="product">
                    <img src="/proyecto/public/img/ModuloVentas/Products/añadir_categoria.png" alt="añadirCategoria">
                </div>
            </a>
        </div>
    </div>
    <div id="categoriaModal" class="modal">
        <div class="modal-content">
            <span class="cerrar-modal">&times;</span>
            <div id="contenidoCategoria"></div>
        </div>
    </div>  
</div>
<script src="/proyecto/public/js/nueva_categoria_ventas.js"></script>
</body>
</html>