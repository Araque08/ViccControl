<?php
session_start();
include("../../../conexionBD/conexion.php");

if (!isset($_GET['id'])) {
    echo "ID no proporcionado";
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

$id = intval($_GET['id']);
$id_restaurante = $_SESSION['id_restaurante'];

$sql = "SELECT id_proveedor, nombre_proveedor, rut_proveedor, telefono_proveedor, direccion_proveedor, email_proveedor, ciudad, estado FROM Proveedores WHERE id_proveedor = ? AND fk_id_restaurante = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id, $id_restaurante);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Proveedor no encontrado";
    exit;
}

$proveedor = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" type="image/png" href="../../../public/favicon.png">
</head>
<body>
    <h2>Editar Proveedor</h2>
    <form action="/../../../controller/Modulos/clientes_proveedores/actualizar_proveedor.php" method="POST">
        <input type="hidden" name="id_proveedor" value="<?= $proveedor['id_proveedor'] ?>">
        <input type="text" name="nombre_proveedor" value="<?= htmlspecialchars($proveedor['nombre_proveedor']) ?>" required>
        <input type="text" name="rut_proveedor" value="<?= htmlspecialchars($proveedor['rut_proveedor']) ?>" required>
        <input type="text" name="direccion" value="<?= htmlspecialchars($proveedor['direccion_proveedor']) ?>" required>
        <input type="text" name="contacto" value="<?= htmlspecialchars($proveedor['telefono_proveedor']) ?>" required>
        <input type="email" name="email" value="<?= htmlspecialchars($proveedor['email_proveedor']) ?>" required>
        <input type="text" name="ciudad" value="<?= htmlspecialchars($proveedor['ciudad']) ?>" required>
        <select name="proveeActividad">
            <option value="Activo" <?= $proveedor['estado'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
            <option value="Inactivo" <?= $proveedor['estado'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
        <button type="submit">Actualizar</button>
    </form>

<script src="../../../public/js/editar_proveedor.js"></script>
</body>
</html>