<?php
include("../../conexionBD/conexion.php");
session_start();

// Verifica que el usuario sea un administrador
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'Administrador') {
    echo "Acceso no autorizado";
    exit;
}

// Validación básica del formulario
if (!isset($_POST['empleado'], $_POST['usuario'], $_POST['contrasena'], $_POST['id_rol'])) {
    header("Location: ../../views/admin/usuarios.php?error=Campos_incompletos");
    exit;
}

// Captura de datos
$id_empleado = intval($_POST['empleado']);
$usuario = trim($_POST['usuario']);
$contrasena = trim($_POST['contrasena']);
$rol_id = intval($_POST['id_rol']);
$id_restaurante = $_SESSION['id_restaurante'];

// Verificar que el nombre de usuario no exista
$verificarUsuario = $conexion->prepare("SELECT id_usuario FROM Usuario WHERE nombre_usuario = ?");
$verificarUsuario->bind_param("s", $usuario);
$verificarUsuario->execute();
$res = $verificarUsuario->get_result();

if ($res->num_rows > 0) {
    header("Location: ../../views/admin/usuarios.php?error=Usuario_ya_existente");
    exit;
}

// Crear usuario
$sql2 = "INSERT INTO Usuario (nombre_usuario, contrasena, fk_id_empleado) VALUES (?, ?, ?)";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param("ssi", $usuario, $contrasena, $id_empleado);

if ($stmt2->execute()) {
    $id_usuario = $stmt2->insert_id;

    // Asignar rol
    $sql3 = "INSERT INTO UsuarioRol (fk_id_usuario, fk_id_rol) VALUES (?, ?)";
    $stmt3 = $conexion->prepare($sql3);
    $stmt3->bind_param("ii", $id_usuario, $rol_id);
    
    if ($stmt3->execute()) {
        header("Location: ../../views/admin/usuarios.php?success=Usuario_creado");
        exit;
    } else {
        header("Location: ../../views/admin/usuarios.php?error=Fallo_al_asignar_rol");
        exit;
    }
} else {
    header("Location: ../../views/admin/usuarios.php?error=Fallo_al_crear_usuario");
    exit;
}
?>
