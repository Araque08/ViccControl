<?php
include("../../../conexionBD/conexion.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_proveedor'];
    $nombre = $_POST['nombre_proveedor'];
    $rut = $_POST['rut_proveedor'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['contacto'];
    $email = $_POST['email'];
    $ciudad = $_POST['ciudad'];
    $estado = $_POST['proveeActividad'];
    $id_restaurante = $_SESSION['id_restaurante'];

    $sql = "UPDATE Proveedores SET 
                nombre_proveedor = ?, 
                rut_proveedor = ?, 
                direccion_proveedor = ?, 
                telefono_proveedor = ?, 
                email_proveedor = ?, 
                ciudad = ?, 
                estado = ?
            WHERE id_proveedor = ? AND fk_id_restaurante = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssssii", $nombre, $rut, $direccion, $telefono, $email, $ciudad, $estado, $id, $id_restaurante);
    
    if ($stmt->execute()) {
        header("Location: ../../../views/modules/clientes_proveedores/proveedores.php?editado=1");
        exit;
    } else {
        echo "Error al actualizar proveedor.";
    }
}
?>
