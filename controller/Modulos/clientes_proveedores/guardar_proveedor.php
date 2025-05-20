<?php
session_start();

if (!isset($_SESSION['Usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

include("../../../conexionBD/conexion.php");

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener datos del formulario
    $nombre = $_POST['nombre_proveedor'];
    $rut = $_POST['rut_proveedor'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['contacto'];
    $email = $_POST['email'];
    $ciudad = $_POST['ciudad'];
    $estado = $_POST['proveeActividad'];
    $fk_restaurante = $_SESSION['id_restaurante'];

    // Validar que el correo no exista
    $sql_check = "SELECT id_proveedor FROM Proveedores WHERE email_proveedor = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "❌ Ya existe un proveedor con ese correo.";
        exit;
    }

    // Insertar proveedor
    $sql_insert = "INSERT INTO Proveedores 
        (nombre_proveedor, rut_proveedor, direccion_proveedor, telefono_proveedor, email_proveedor, ciudad, estado, fk_id_restaurante)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("sisssssi", $nombre, $rut, $direccion, $telefono, $email, $ciudad, $estado, $fk_restaurante);

    if ($stmt->execute()) {
        header("Location: ../../../views/modules/clientes_proveedores/proveedores.php?success=1");
        exit;
    } else {
        echo "Error al guardar proveedor.";
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "Método no permitido.";
}
?>
