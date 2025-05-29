<?php
session_start();
include("../../../conexionBD/conexion.php");

$id_restaurante = $_SESSION['id_restaurante'];

$sql = "
SELECT e.id_empleado, e.cedula, e.nombre_empleado, e.apellido_empleado, e.salario_empleado
FROM Empleado e
WHERE e.fk_id_restaurante = ?
AND e.id_empleado NOT IN (
    SELECT u.fk_id_empleado
    FROM Usuario u
    JOIN UsuarioRol ur ON u.id_usuario = ur.fk_id_usuario
    JOIN Rol r ON ur.fk_id_rol = r.id_rol
    WHERE r.nombre_rol = 'Administrador'
)
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_restaurante);
$stmt->execute();
$resultado = $stmt->get_result();

$deducciones = $conexion->query("SELECT * FROM Deducciones");
$lista_deducciones = [];
while ($d = $deducciones->fetch_assoc()) {
    $lista_deducciones[] = $d;
}

?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Gestión de Nómina</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../../../public/css/nomina.css">
    <link rel="icon" type="image/png" href="../../../public/favicon.png">
</head>
<body>

<div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="../../../views/modules/rrhh_nomina/rrhh_nomina_menu.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Materia Prima</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>

<h2>📄 Nómina del Mes</h2>

<form action="../../../controller/Modulos/rrhh_nomina/generar_nomina.php" method="POST">
    <label>Periodo: <input type="text" name="periodo" placeholder="Mayo 2025" required></label>
    <div class="divider calendario" style="margin: 20px 0;">
        <label><strong>Seleccionar fecha de inventario:</strong></label>
        <div class="calendario" id="calendar"></div>
        <input type="hidden" name="fecha_nomina" id="fecha_nomina">
    </div>

    <table>
    <thead>
        <tr>
            <th>Empleado</th>
            <th>Salario Base</th>
            <th>Bonificaciones</th>
            <th>Deducciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($e = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $e['nombre_empleado'] . ' ' . $e['apellido_empleado'] ?></td>
                <td>
                    <input type="number" name="salario_bruto[<?= $e['id_empleado'] ?>]" 
                           value="<?= $e['salario_empleado'] ?>" readonly>
                </td>
                <td>
                    <input type="number" name="bonificaciones[<?= $e['id_empleado'] ?>]" value="0" step="0.01">
                </td>
                <td class="align-left">
                    <?php foreach ($lista_deducciones as $d): ?>
                        <label style="display: block;">
                            <input type="checkbox" 
                                   name="deducciones[<?= $e['id_empleado'] ?>][]" 
                                   value="<?= $d['id_deduccion'] ?>">
                            <?= $d['tipo_deduccion'] ?> (<?= $d['porcentaje_deduccion'] ?>%)
                        </label>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>


    <div class="actions">
        <button type="submit">💾 Guardar Nómina</button>
    </div>
</form>

<form method="GET" action="../../../modules/generar_pdf_nomina.php">
    <label>Seleccionar Fecha:</label>
    <input type="date" name="fecha" required>
    <button type="submit">Generar PDF</button>
    </form>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    // Inicializar calendario embebido (inline)
    flatpickr("#calendar", {
        inline: true,
        dateFormat: "Y-m-d",
        defaultDate: new Date(),
        onChange: function(selectedDates, dateStr) {
        document.getElementById("fecha_nomina").value = dateStr;
    }

    });

    // Calcular diferencia entre stock en bodega y stock disponible
    document.querySelectorAll('.stock-input').forEach(input => {
        input.addEventListener('input', function () {
            const id = this.dataset.id;
            const stockDisp = parseFloat(this.dataset.stockdisp);
            const stockBodega = parseFloat(this.value) || 0;
            const diferencia = stockBodega - stockDisp;

            document.getElementById(`diferencia-${id}`).textContent = diferencia;
        });
    });
</script>

</body>
</html>
