<?php
include("../../conexionBD/conexion.php"); // Conexión a la base de datos

// 📥 Datos recibidos del formulario
$nombre_restaurante = $_POST['nombre_restaurante'];
$direccion = $_POST['direccion'];
$membresia = $_POST['tipo_membresia'];
$hoy = date('Y-m-d'); // Fecha actual para inicio de membresía

$nombre_admin = $_POST['nombre_admin'];
$apellido_admin = $_POST['apellido_admin'];
$usuario_admin = $_POST['usuario_admin'];
$clave_admin = $_POST['clave_admin'];

// 1️⃣ Crear restaurante (membresía de 1 año)
$sql1 = "INSERT INTO Restaurante (nombre_restaurante, direccion, tipo_membresia, fecha_inicio_membresia, fecha_fin_membresia)
VALUES (?, ?, ?, ?, DATE_ADD(?, INTERVAL 1 YEAR))";
$stmt1 = $conexion->prepare($sql1);
$stmt1->bind_param("sssss", $nombre_restaurante, $direccion, $membresia, $hoy, $hoy);
$stmt1->execute();
$id_restaurante = $stmt1->insert_id; // ID del nuevo restaurante

// 2️⃣ Crear empleado administrador en ese restaurante
$sql2 = "INSERT INTO Empleado (nombre_empleado, apellido_empleado, fk_id_restaurante)
VALUES (?, ?, ?)";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param("ssi", $nombre_admin, $apellido_admin, $id_restaurante);
$stmt2->execute();
$id_empleado = $stmt2->insert_id;

// 3️⃣ Crear usuario para ese empleado
$sql3 = "INSERT INTO Usuario (nombre_usuario, contrasena, fk_id_empleado)
VALUES (?, ?, ?)";
$stmt3 = $conexion->prepare($sql3);
$stmt3->bind_param("ssi", $usuario_admin, $clave_admin, $id_empleado);
$stmt3->execute();
$id_usuario = $stmt3->insert_id;

// 4️⃣ Asignar rol de administrador (usualmente id_rol = 1, pero verifica)
$rol_admin = 1;
$sql4 = "INSERT INTO UsuarioRol (fk_id_usuario, fk_id_rol) VALUES (?, ?)";
$stmt4 = $conexion->prepare($sql4);
$stmt4->bind_param("ii", $id_usuario, $rol_admin);
$stmt4->execute();

// ✅ Confirmación
echo "✅ Restaurante y administrador creados correctamente.";
?>
