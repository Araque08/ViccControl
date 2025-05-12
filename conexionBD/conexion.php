<?php
$host = "database-vicccontrol.c9aqi68sslgg.us-east-2.rds.amazonaws.com"; // IP
$usuario = "admin"; // usuario de MySQL
$clave = "Axeso51357perezs"; // contraseña
$base_de_datos = "ViccControl";

$conexion = new mysqli($host, $usuario, $clave, $base_de_datos);


if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
