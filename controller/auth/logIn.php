<?php

include("../../conexionBD/conexion.php"); // Conexión a la base de datos
session_start(); // Inicia la sesión para guardar datos del usuario

define('BASE_URL', '../../../ViccControl');


// Datos que vienen del formulario de login
$compania = $_POST['compania'];
$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

// Consulta SQL para buscar el usuario con su restaurante y rol
$sql = "SELECT u.id_usuario, u.nombre_usuario, u.contrasena,
                r.id_restaurante, r.nombre_restaurante,
                Rol.id_rol, Rol.nombre_rol, Rol.ruta_inicio
        FROM Usuario u
        JOIN Empleado e ON u.fk_id_empleado = e.id_empleado
        JOIN Restaurante r ON e.fk_id_restaurante = r.id_restaurante
        JOIN UsuarioRol ur ON u.id_usuario = ur.fk_id_usuario
        JOIN Rol ON ur.fk_id_rol = Rol.id_rol
        WHERE u.nombre_usuario = ? AND u.contrasena = ? AND r.nombre_restaurante = ? AND r.estado = 'activo'";

// Prepara la consulta para evitar inyección SQL
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $usuario, $contrasena, $compania);
$stmt->execute();
$resultado = $stmt->get_result();

// Si el usuario existe, se inicia sesión y se redirige según su rol
if ($resultado->num_rows === 1) {
    $datos = $resultado->fetch_assoc(); // Extrae los datos como array asociativo

    // Guarda los datos de sesión para el usuario
    $_SESSION['id_usuario'] = $datos['id_usuario'];
    $_SESSION['Usuario'] = $datos['nombre_usuario'];
    $_SESSION['id_restaurante'] = $datos['id_restaurante'];
    $_SESSION['Restaurante'] = $datos['nombre_restaurante'];
    $_SESSION['Rol'] = $datos['nombre_rol'];
    $_SESSION['ruta_inicio'] = $datos['ruta_inicio'];  // Asegúrate de que esto se asigna correctamente

    // Redirige automáticamente a la ruta asignada al rol (usando router.php)
    
    echo($_SESSION['ruta_inicio']);
    header("Location: " . BASE_URL .$_SESSION['ruta_inicio']);
    exit;
} else {
    // Si no coincide el usuario, contraseña o restaurante
    header("Location: ../../index.php?error=1");
    exit;
}

// Cierra la conexión a la base de datos
$stmt->close();
$conexion->close();
?>





