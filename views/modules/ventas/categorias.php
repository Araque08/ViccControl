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

$sql = "SELECT * FROM CategoriaProducto";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Productos</title>
    <link rel="stylesheet" href="/../../../public/css/menu.css">
    <link rel="stylesheet" href="/../../../public/css/ventas.css">
    <link rel="stylesheet" href="/../../../public/css/modal.css">
    <link rel="icon" type="image/png" href="../../../public/favicon.png">
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
            <img src="/../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
</div>
<div class="productos">
    <?php if (isset($_GET['creado'])): ?>
        <div class="alert-success">Categoría creada correctamente.</div>
    <?php elseif (isset($_GET['editado'])): ?>
        <div class="alert-success">Categoría actualizada correctamente.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert-error">
            <?php
            switch ($_GET['error']) {
                case 'imagen_error': echo "Error al subir la imagen."; break;
                case 'imagen_requerida': echo "La imagen es obligatoria para crear una nueva categoría."; break;
                case 'fallo_actualizar': echo "No se pudo actualizar la categoría."; break;
                case 'fallo_crear': echo "No se pudo crear la categoría."; break;
                case 'sin_sesion': echo "Sesión de restaurante no válida."; break;
                default: echo "Ocurrió un error inesperado."; break;
            }
            ?>
        </div>
    <?php endif; ?>
    <div class="container-productos">

        <?php
            while ($row = $result->fetch_assoc()) {
                echo '<div class="products-container">
                        <a href="ver_categoria.php?id_categoria=' . $row['id_categoria'] . '">
                            <div class="product">
                                <img src="' . $row['imagen_categoria'] . '" alt="' . $row['nombre_categoria'] . '">
                                <p>' . $row['nombre_categoria'] . '</p>
                                <div class="menu-dots">
                                        <div id="menu-' . $row['id_categoria'] . '" class="options-menu">
                                            <a href="javascript:void(0)" onclick="mostrarEdicion(' . $row['id_categoria'] . ')">Editar</a>                   
                                            <a href="/../../../controller/Modulos/Ventas/eliminar_categoria.php?id=' . $row['id_categoria'] . '">Eliminar</a>
                                        </div>
                                </div>
                                <i class="fa-solid fa-ellipsis-vertical" onclick="toggleMenu(' . $row['id_categoria'] . ')"></i>
                            </div>
                        </a>
                    </div>';
            }
        ?>

        <div class="products-container" id="products-container">
            <a href="#" onclick="mostrarCategoria()">
                <div class="product">
                    <img src="/../../../public/img/ModuloVentas/Products/añadir_categoria.png" alt="añadirCategoria">
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
    <div id="editarModal" class="modal">
        <div class="modal-content">
            <span class="cerrar-modal">&times;</span>
            <div id="contenidoEditar"></div>
        </div>
    </div>  
</div>
<script src="/public/js/nueva_categoria_ventas.js"></script>
</body>
</html>