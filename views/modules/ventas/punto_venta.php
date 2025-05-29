<?php
session_start();

include("../../../conexionBD/conexion.php");

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

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

$sql = "SELECT id_categoria, nombre_categoria, imagen_categoria 
        FROM CategoriaProducto 
        WHERE fk_id_restaurante = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_restaurante);
$stmt->execute();
$resultado = $stmt->get_result();

// ... conexión y sesión
$query = "SELECT id_medio_pago, nombre_medio_pago FROM MedioPago";
$resultMediosPago = $conexion->query($query);

$mediosPago = [];
if ($resultMediosPago && $resultMediosPago->num_rows > 0) {
    while ($row = $resultMediosPago->fetch_assoc()) {
        $mediosPago[] = [
            'id' => $row['id_medio_pago'],
            'nombre' => $row['nombre_medio_pago']
        ];
    }
}

?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <title>Punto de Venta</title>
  <link rel="stylesheet" href="../../../public/css/punto_venta.css">
  <link rel="icon" type="image/png" href="../../../public/favicon.png">
</head>
<body>

<header>
  <div class="regresar">
    <a href="ventas_menu.php">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
  </div>
  Punto de venta
</header>

<main>
  <section class="productos">
    <div class="categorias-container" id="categorias-container">
      <?php while ($row = $resultado->fetch_assoc()) { ?>
        <div class="categoria-card" data-id="<?= $row['id_categoria'] ?>">
          <img src="<?= $row['imagen_categoria'] ?>" alt="<?= $row['nombre_categoria'] ?>">
          <p><?= $row['nombre_categoria'] ?></p>
        </div>
      <?php } ?>
    </div>

    <div id="productos-de-categoria" class="productos-container" style="display: none;"></div>

    <button id="volver-categorias" style="display: none;">⬅️ Volver a categorías</button>
  </section>

  <section class="panel-venta"  id="tabla-factura">
    <div class="tabla-venta">
      <table>
        <thead>
          <tr>
            <th>Item</th>
            <th>Producto</th>
            <th>Cant</th>
            <th>Total</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <!-- Aquí se agregan productos -->
        </tbody>
      </table>
    </div>

    <div class="tipo-pago">
    <select id="medio-pago" name="medio_pago">
      <?php foreach ($mediosPago as $medio) : ?>
        <option value="<?= $medio['id'] ?>"><?= htmlspecialchars($medio['nombre']) ?></option>
      <?php endforeach; ?>
    </select>



      <label><input type="checkbox" /> Proppina Incluida</label>
    </div>

    <div class="total-venta">
      <p>SUBTOTAL: <span id="subtotal">$0</span></p>
      <p>IPM(8%): <span id="ipm">$0</span></p>
      <p><strong>TOTAL: <span id="total">$0</span></strong></p>
    </div>

    <div class="botones">
      <button class="btn-limpiar">Limpiar</button>
      <button class="btn-factura">Generar Factura</button>
    </div>
  </section>
</main>

<!-- Modal datos cliente -->
<div id="modal-cliente" class="modal" style="display:none;">
  <div class="modal-content">
    <span id="cerrar-modal" style="cursor:pointer; float:right; font-size:20px;">&times;</span>
    <h3>Datos del Cliente</h3>
    <form id="form-cliente">
      <label for="nombre_cliente">Nombre:</label>
      <input type="text" id="nombre_cliente" name="nombre_cliente" required><br><br>

      <label for="nombre_cliente">Cedula:</label>
      <input type="text" id="cedula" name="cedula" required><br><br>

      <label for="telefono_cliente">Teléfono:</label>
      <input type="text" id="telefono_cliente" name="telefono_cliente" required><br><br>

      <label for="direccion_cliente">Dirección:</label>
      <input type="text" id="direccion_cliente" name="direccion_cliente"><br><br>

      <label for="email_cliente">Email:</label>
      <input type="email" id="email_cliente" name="email_cliente"><br><br>

      <button type="submit">Confirmar</button>
    </form>
  </div>
</div>

<style>
.modal {
  position: fixed; 
  z-index: 9999; 
  left: 0; top: 0; 
  width: 100%; height: 100%; 
  background-color: rgba(0,0,0,0.5);
  display: flex; 
  justify-content: center; 
  align-items: center;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 5px;
  width: 350px;
}
</style>



<script src="/public/js/punto_venta.js"></script>
<script src="/public/js/modal_cliente.js"></script>


</body>
</html>
