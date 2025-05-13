<?php
session_start();

// Validación de SuperAdmin
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'SuperAdmin') {
    header("Location: ../../index.php");
    exit;
}

include("../../conexionBD/conexion.php");

// Verificar si llegaron los datos necesarios
if (isset($_POST['id_restaurante'], $_POST['modulo'], $_POST['estado_actual'])) {
    $id_restaurante = $_POST['id_restaurante'];
    $modulo = $_POST['modulo'];
    $estado_actual = $_POST['estado_actual'];

    // Validación segura de nombres de módulo
    $modulos_validos = [
        'ventas',
        'contabilidad',
        'compras_inventario',
        'clientes_proveedores',
        'rrhh_nomina'
    ];

    if (in_array($modulo, $modulos_validos)) {
        $nuevo_estado = ($estado_actual === 'activo') ? 'inactivo' : 'activo';

        // Revisar si ya existe el módulo asignado
        $check = $conexion->prepare("SELECT id_modulo_restaurante FROM ModuloRestaurante WHERE fk_id_restaurante = ? AND nombre_modulo = ?");
        $check->bind_param("is", $id_restaurante, $modulo);
        $check->execute();
        $resultado = $check->get_result();

        if ($resultado->num_rows > 0) {
            // Si ya existe, solo actualizar
            $sql = "UPDATE ModuloRestaurante SET estado = ? WHERE fk_id_restaurante = ? AND nombre_modulo = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sis", $nuevo_estado, $id_restaurante, $modulo);
        } else {
            // Si no existe, lo insertamos
            $sql = "INSERT INTO ModuloRestaurante (fk_id_restaurante, nombre_modulo, estado) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iss", $id_restaurante, $modulo, $nuevo_estado);
        }

        if ($stmt->execute()) {
            header("Location: ../../views/superadmin/superadmin.php");
            exit;
        } else {
            echo "❌ Error al guardar el estado del módulo.";
        }

        $stmt->close();
        $check->close();
    } else {
        echo "❌ Módulo no permitido.";
    }
} else {
    echo "❌ Datos incompletos.";
}

$conexion->close();
?>
