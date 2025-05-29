<?php
session_start();
include("../../../conexionBD/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capturar los datos del formulario
    $nombre       = $_POST['nombre'];
    $apellido     = $_POST['apellido'];
    $documento    = $_POST['numero_documento'];
    $lugar_nac    = $_POST['lugar_nacimiento'] ?? null;
    $fecha_nac    = $_POST['fecha_nacimiento'] ?? null;
    $estado_civil = $_POST['estado_civil'] ?? null;
    $direccion    = $_POST['direccion'] ?? null;
    $telefono     = $_POST['telefono'] ?? null;
    $email        = $_POST['email'] ?? null;
    $cuenta_banco = $_POST['cuenta_banco'] ?? null;
    $salario      = $_POST['salario'] ?? 0;
    $funciones    = $_POST['funciones'] ?? null;
    $tiene_hijos  = isset($_POST['tiene_hijos']) ? intval($_POST['tiene_hijos']) : 0;
    $cantidad_hijos = $_POST['cantidad_hijos'] ?? 0;
    $fk_id_cargo    = $_POST['fk_id_cargo'];
    $fk_id_contrato = $_POST['fk_id_contrato'];
    $estado_empleado = $_POST['estado_empleado'];
    $fk_id_restaurante = $_SESSION['id_restaurante'] ?? 1;

    // Validación mínima
    if (empty($nombre) || empty($apellido) || empty($documento) || empty($fk_id_cargo) || empty($fk_id_contrato)) {
        die("Error: Faltan campos obligatorios.");
    }

    // Verificar si la cédula ya existe
    $check = $conexion->prepare("SELECT id_empleado FROM Empleado WHERE cedula = ?");
    $check->bind_param("s", $documento);
    $check->execute();
    $resultado = $check->get_result();

    if ($resultado->num_rows > 0) {
        // Si existe, actualizar
        $empleado = $resultado->fetch_assoc();
        $id_empleado = $empleado['id_empleado'];

        $sql = "UPDATE Empleado SET 
                    nombre_empleado = ?, apellido_empleado = ?, cedula = ?, lugar_nacimiento = ?, 
                    fecha_nacimiento = ?, estado_civil = ?, direccion_empleado = ?, 
                    telefono_empleado = ?, email_empleado = ?, cuenta_banco = ?, 
                    salario_empleado = ?, funciones_empleado = ?, tiene_hijos = ?, 
                    cantidad_hijos = ?, fk_id_cargo = ?, fk_id_contrato = ?, 
                    estado_empleado = ?, fk_id_restaurante = ?
                WHERE id_empleado = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssssssdsssiiisii", 
            $nombre, $apellido, $documento, $lugar_nac, $fecha_nac, $estado_civil,
            $direccion, $telefono, $email, $cuenta_banco, $salario,
            $funciones, $tiene_hijos, $cantidad_hijos, 
            $fk_id_cargo, $fk_id_contrato, $estado_empleado, $fk_id_restaurante, $id_empleado
        );

    } else {
        // Si no existe, insertar
        $sql = "INSERT INTO Empleado (
                    nombre_empleado, apellido_empleado, cedula, lugar_nacimiento, fecha_nacimiento, 
                    estado_civil, direccion_empleado, telefono_empleado, email_empleado, 
                    cuenta_banco, salario_empleado, funciones_empleado, tiene_hijos, 
                    cantidad_hijos, fk_id_cargo, fk_id_contrato, estado_empleado, fk_id_restaurante
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssssssdsssiiisi", 
            $nombre, $apellido, $documento, $lugar_nac, $fecha_nac, $estado_civil,
            $direccion, $telefono, $email, $cuenta_banco, $salario,
            $funciones, $tiene_hijos, $cantidad_hijos, 
            $fk_id_cargo, $fk_id_contrato, $estado_empleado, $fk_id_restaurante
        );
    }

    if ($stmt->execute()) {
        header("Location: ../../../views/modules/rrhh_nomina/Empleados.php?success=1");
        exit;
    } else {
        echo "Error al guardar: " . $stmt->error;
    }

    $stmt->close();
    $check->close();
    $conexion->close();

} else {
    echo "Acceso no permitido.";
}
?>

