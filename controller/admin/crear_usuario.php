<?php
include("/proyecto/conexionBD/conexion.php");  // Conecta con la base de datos
session_start();          // Usa sesión para identificar qué admin está creando al empleado

// Verifica que el usuario logueado sea un administrador
if ($_SESSION['rol'] !== 'Administrador') {
    echo "Acceso no autorizado"; // Bloquea a otros roles
    exit;
}

// Recibe los datos del formulario de creación de empleado
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$usuario = $_POST['usuario'];       // nombre de usuario para login
$contrasena = $_POST['contrasena']; // contraseña para login
$rol_id = $_POST['id_rol'];         // ID del rol que tendrá este usuario

// El ID del restaurante se toma de la sesión del admin logueado
$id_restaurante = $_SESSION['id_restaurante'];

// 1️⃣ Insertar al empleado en la tabla Empleado
$sql1 = "INSERT INTO Empleado (nombre_empleado, apellido_empleado, fk_id_restaurante) VALUES (?, ?, ?)";
$stmt1 = $conexion->prepare($sql1);
$stmt1->bind_param("ssi", $nombre, $apellido, $id_restaurante);
$stmt1->execute();
$id_empleado = $stmt1->insert_id; // Obtener el ID generado automáticamente

// 2️⃣ Insertar al usuario en la tabla Usuario y asociarlo con el empleado
$sql2 = "INSERT INTO Usuario (nombre_usuario, contraseña, fk_id_empleado) VALUES (?, ?, ?)";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param("ssi", $usuario, $contrasena, $id_empleado);
$stmt2->execute();
$id_usuario = $stmt2->insert_id;

// 3️⃣ Asignar el rol al usuario en la tabla intermedia Usuario_Rol
$sql3 = "INSERT INTO Usuario_Rol (fk_id_usuario, fk_id_rol) VALUES (?, ?)";
$stmt3 = $conexion->prepare($sql3);
$stmt3->bind_param("ii", $id_usuario, $rol_id);
$stmt3->execute();

// Confirmación final
echo "✅ Empleado creado con éxito";
?>