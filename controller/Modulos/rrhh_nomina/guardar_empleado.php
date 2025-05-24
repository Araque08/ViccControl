<?php
session_start();
include("../../../conexionBD/conexion.php");

// Verifica que venga de un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturar los datos del formulario
    $nombre       = $_POST['nombre'];
    $apellido     = $_POST['apellido'];
    $lugar_nac    = $_POST['lugar_nacimiento'] ?? null;
    $fecha_nac    = $_POST['fecha_nacimiento'] ?? null;
    $estado_civil = $_POST['estado_civil'] ?? null;
    $direccion    = $_POST['direccion'] ?? null;
    $telefono     = $_POST['telefono'] ?? null;
    $email        = $_POST['email'] ?? null;
    $cuenta_banco = $_POST['cuenta_banco'] ?? null;
    $salario      = $_POST['salario'] ?? 0;
    $funciones    = $_POST['funciones'] ?? null;
    $documento    = $_POST['numero_documento'];
    $tiene_hijos  = isset($_POST['tiene_hijos']) ? intval($_POST['tiene_hijos']) : 0;
    $cantidad_hijos = $_POST['cantidad_hijos'] ?? 0;
    $fk_id_cargo    = $_POST['fk_id_cargo'];
    $fk_id_contrato = $_POST['fk_id_contrato'];
    $fk_id_restaurante = $_SESSION['id_restaurante'] ?? 1; // Asegúrate que venga de la sesión

    // Validación mínima
    if (empty($nombre) || empty($apellido) || empty($documento) || empty($fk_id_cargo) || empty($fk_id_contrato)) {
        die("Error: Faltan campos obligatorios.");
    }

    // Consulta SQL preparada
    $sql = "INSERT INTO Empleado (
        nombre_empleado, apellido_empleado, lugar_nacimiento, fecha_nacimiento, estado_civil,
        direccion_empleado, telefono_empleado, email_empleado, cuenta_banco, salario_empleado,
        funciones_empleado, tiene_hijos, cantidad_hijos, fk_id_cargo, fk_id_contrato, fk_id_restaurante
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssdsssiiii", 
        $nombre, $apellido, $lugar_nac, $fecha_nac, $estado_civil,
        $direccion, $telefono, $email, $cuenta_banco, $salario,
        $funciones, $tiene_hijos, $cantidad_hijos, 
        $fk_id_cargo, $fk_id_contrato, $fk_id_restaurante
    );

    if ($stmt->execute()) {
        header("Location: ../../views/modules/rrhh_nomina/Empleados.php?success=1");
        exit;
    } else {
        echo "Error al guardar: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();

} else {
    echo "Acceso no permitido.";
}
?>
