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

$sql_proveedores = "SELECT 
    id_proveedor,
    nombre_proveedor,
    rut_proveedor,
    telefono_proveedor,
    direccion_proveedor,
    ciudad,
    email_proveedor
FROM Proveedores
WHERE estado = 'Activo' AND fk_id_restaurante = ?";
$stmt = $conexion->prepare($sql_proveedores);
$stmt->bind_param("i", $id_restaurante);
$stmt->execute();
$proveedores_result = $stmt->get_result();

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
    <link rel="stylesheet" href="../../../public/css/modal.css">
</head>
<body>
<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="clientes_proveedores_menu.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Proveedores</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
<!--Formulario compras -->
    <?php if (isset($_GET['editado']) && $_GET['editado'] == 1): ?>
        <div class="alert-success">Proveedor actualizado correctamente.</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['eliminado']) && $_GET['eliminado'] == 1): ?>
    <div class="alert-success">Proveedor eliminado correctamente.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert-error">
            <?php
                if ($_GET['error'] == 'compras_asociadas') {
                    echo "No se puede eliminar el proveedor porque tiene compras asociadas.";
                } else {
                    echo "Error al eliminar el proveedor.";
                }
            ?>
        </div>
    <?php endif; ?>


    <div class="container">
        <div class="container_formulario">
            
            <!-- Formulario para crear nueva materia prima -->
            <div class="form-section">
                <form action="/../../../controller/Modulos/clientes_proveedores/guardar_proveedor.php" method="POST">
                    <input type="text" name="nombre_proveedor" id="nombre_proveedor" placeholder="Nombre Proveedor" required>
                    <input type="text" name="rut_proveedor" id="rut_proveedor" placeholder="Rut Proveedor" required>
                    <input type="text" name="direccion" id="direccion" placeholder="Direccion" required>
                    <input type="text" name="contacto" id="contacto" placeholder="Contacto" required>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <input type="text" name="ciudad" id="ciudad" placeholder="Ciudad" required>
                    <select name="proveeActividad" id="proveeActividad">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>

                    <div class="container-button">
                        <button type="submit">Crear</button>
                    </div>
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
                        <th>Rut</th>
                        <th>Tel</th>
                        <th>Direccion</th>
                        <th>Email</th>
                        <th>Ciudad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $proveedores_result ->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['id_proveedor'] . "</td>
                                <td>" . $row['nombre_proveedor'] . "</td>
                                <td>" . $row['rut_proveedor'] . "</td>
                                <td>" . $row['telefono_proveedor'] . "</td>
                                <td>" . $row['direccion_proveedor'] . "</td>
                                <td>" . $row['email_proveedor'] . "</td>
                                <td>" . $row['ciudad'] . "</td>
                                <td>
                                    <button onclick=\"mostrarModal(" . $row['id_proveedor'] . ")\">✏️</button>
                                    <a href='../../../controller/Modulos/clientes_proveedores/eliminar_proveedor.php?id=" . $row['id_proveedor'] . "' onclick=\"return confirm('¿Estás seguro de eliminar este proveedor?')\">🗑️</a>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>  
    </div>
    <div id="nuevoModal" class="modal">
        <div class="modal-content">
            <span class="cerrar-modal">&times;</span>
            <div id="contenidoModal"></div>
        </div>
    </div>

    <script src="/../../../public/js/editar_proveedor.js"></script>
</body>
</html>