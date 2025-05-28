<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
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

$sql = "SELECT 
            mp.id_materia_prima,
            mp.nombre_materia_prima,
            mp.unidad_materia_prima,
            mp.stock_disp,
            IFNULL(i.stock_bodega, 0) AS stock_bodega,
            IFNULL(i.diferencia_stock, 0) AS diferencia_stock
        FROM MateriaPrima mp
        LEFT JOIN Inventario i 
            ON mp.id_materia_prima = i.fk_id_materia_prima 
            AND i.fk_id_restaurante = mp.fk_id_restaurante
        WHERE mp.fk_id_restaurante = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_restaurante']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <title>Modulo de Control</title>
    <link rel="stylesheet" href="../../../public/css/menu.css">
    <link rel="stylesheet" href="../../../public/css/compras_inventario.css">
</head>
<body>

<div class="header-ventas">
    <div class="container-header">
        <div class="compañia">
            <div class="container-subModulo">
                <div class="regresar">
                    <a href="compras_inventario_menu.php">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <h1 class="nombre-pagina"><strong>Inventario</strong></h1>
            </div>
        </div>
        <div class="logo">
            <img src="/../../../public/img/ViccControlImg.png" alt="logo de la compañia">
        </div>
    </div>
<div class="container_buscar">
    <form class="form_inventario" action="/../../../controller/Modulos/compras_inventario/guardar_inventario.php" method="POST">
        <div class="divider calendario" style="margin: 20px 0;">
            <label><strong>Seleccionar fecha de inventario:</strong></label>
            <div class="calendario" id="calendar"></div>
            <input type="hidden" name="fecha_inventario" id="fecha_inventario">
        </div>
        <div class="divider tabla">
        <table border="1">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Unidad</th>
                    <th>Stock disp</th>
                    <th>Stock en bodega</th>
                    <th>Diferencia</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) {
                    $id = $row['id_materia_prima'];
                    $stockDisp = $row['stock_disp'];
                    $stockBodega = $row['stock_bodega'];
                    ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= $row['nombre_materia_prima'] ?></td>
                        <td><?= $row['unidad_materia_prima'] ?></td>
                        <td><?= $stockDisp ?></td>
                        <td>
                            <input 
                                type="number" 
                                name="stock_bodega[<?= $id ?>]" 
                                value="<?= $stockBodega ?>" 
                                class="stock-input" 
                                data-id="<?= $id ?>"
                                data-stockdisp="<?= $stockDisp ?>"
                                required>
                        </td>
                        <td id="diferencia-<?= $id ?>"><?= $stockBodega - $stockDisp ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <div class="continer_button">
            <button class="guardar_inventario" type="submit">Guardar Cambios</button>
        </div>
        </div>
    </form>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    // Inicializar calendario embebido (inline)
    flatpickr("#calendar", {
        inline: true,
        dateFormat: "Y-m-d",
        defaultDate: new Date(),
        onChange: function(selectedDates, dateStr) {
            document.getElementById("fecha_inventario").value = dateStr;
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


