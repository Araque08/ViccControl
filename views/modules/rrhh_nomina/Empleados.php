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

// Consultar cargos
$sqlCargos = "SELECT id_cargo, nombre_cargo FROM Cargo";
$resultCargos = $conexion->query($sqlCargos);

// Consultar contratos
$sqlContratos = "SELECT id_contrato, nombre_contrato FROM TipoContrato";
$resultContratos = $conexion->query($sqlContratos);

$sql = "SELECT 
            e.id_empleado, 
            e.nombre_empleado, 
            e.apellido_empleado, 
            e.email_empleado,
            e.telefono_empleado,
            tc.nombre_contrato
        FROM Empleado e
        JOIN TipoContrato tc ON e.fk_id_contrato = tc.id_contrato";

$result_empleados = $conexion->query($sql);

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
    <link rel="stylesheet" href="../../../public/css/empleado.css">
</head>
<body>
<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="rrhh_nomina_menu.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Empleado</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>

    <div class="container">
        <div class="container_formulario">

            <!-- Formulario para crear un nuevo empleado -->
            <div class="form-section">
                <h3>Crear nueva materia prima</h3>
                <form action="/../../../controller/Modulos/rrhh_nomina/guardar_empleado.php" method="POST">

                    <div class="datos_personales" >
                        <input type="text" name="nombre" placeholder="Nombre" required>
                        <input type="text" name="apellido" placeholder="Apellido" required>
                        <input type="text" name="numero_documento" placeholder="Numero de Documento">
                        <input type="text" name="lugar_nacimiento" placeholder="Lugar de nacimiento">
                        <input type="date" name="fecha_nacimiento" placeholder="fecha Nacimiento">
                        <select name="estado_civil" >
                            <option value="">--Seleccionar estado civil--</option>
                            <option value="Soltero">Soltero</option>
                            <option value="Casado">Casado</option>
                            <option value="Divorciado">Divorciado</option>
                            <option value="Viudo">Viudo</option>
                        </select>
                        <input type="text" name="cuenta_banco" placeholder="Cuenta de Banco">
                    </div>
                    <div class="datos_derecha">
                            <div class="datos_contacto">
                                <input type="text" name="telefono" placeholder="Telefono">
                                <input type="email" name="email" placeholder="Correo Electronico">
                                <input type="text" name="direccion" placeholder="Direccion">
                            </div>

                            <div class="detalle_contrato">
                                <select name="fk_id_contrato" required>
                                    <option value="">-- Seleccionar Tipo de Contrato --</option>
                                    <?php while ($row = $resultContratos->fetch_assoc()): ?>
                                        <option value="<?= $row['id_contrato'] ?>"><?= htmlspecialchars($row['nombre_contrato']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <select name="fk_id_cargo" required>
                                    <option value="">-- Seleccionar Cargo --</option>
                                    <?php while ($row = $resultCargos->fetch_assoc()): ?>
                                        <option value="<?= $row['id_cargo'] ?>"><?= htmlspecialchars($row['nombre_cargo']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <input type="text" name="funciones" placeholder="funciones">
                                <input type="number" step="0.01" name="salario" placeholder="Salario">
                            </div>

                            <div class="familia">
                                <select name="tiene_hijos" >
                                    <option value="">Hijos</option>
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>

                                <input type="number" name="cantidad_hijos" min="0" placeholder="cantidad de hijos">
                                <button type="submit">Registrar Empleado</button>
                            </div>
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
            <table id="empleadosTable">
                <thead>
                    <tr>
                        <th>Cod</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Cédula</th>
                        <th>Email</th>
                        <th>Tel</th>
                        <th>Contrato</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result_empleados->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['id_empleado'] . "</td>
                                <td>" . $row['nombre_empleado'] . "</td>
                                <td>" . $row['apellido_empleado'] . "</td>
                                <td>" . $row['id_empleado'] . "</td>
                                <td>" . $row['email_empleado'] . "</td>
                                <td>" . $row['telefono_empleado'] . "</td>
                                <td>" . $row['nombre_contrato'] . "</td>
                                <td>
                                    <button onclick=\"editarEmpleado(" . $row['id_empleado'] . ")\">✏️</button>
                                    <button onclick=\"eliminarEmpleado(" . $row['id_empleado'] . ")\">🗑️</button>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
    <!-- Modal para agregar nueva categoría -->
   <div id="nuevaCategoriaModal" class="modal">
        <div class="modal-content">
            <span class="cerrar-modal">&times;</span>
            <div id="contenidoNuevaCategoria"></div>
        </div>
    </div>
    <!-- Modal para editar materia prima -->
    <div id="nuevoModal" class="modal">
        <div class="modal-content">
            <span class="cerrar-modal">&times;</span>
            <div id="contenidoModal"></div>
        </div>
    </div>
</body>
</html>