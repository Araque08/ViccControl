<?php
session_start();
if (!isset($_SESSION['Rol']) || $_SESSION['Rol'] !== 'SuperAdmin') {
    header("Location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Restaurante</title>
    <link rel="icon" type="image/png" href="../../public/favicon.png">
</head>
<body>
    <div class="centrar">
        <!-- Formulario para que el SuperAdmin cree un nuevo restaurante con su administrador -->
        <h1>Crear Restaurante+Admin</h1>


        <form class="form_crearRestaurante" action="../../controller/superadmin/guardar_restaurante_admin.php" method="POST">
            <div class="container_nuevoRestaurante">
                <div class="restaurante">                                                                               
                    <h2>Nuevo Restaurante</h2>
                    <div class="casilla">
                        <label>Nombre: </label>
                        <input type="text" name="nombre_restaurante">
                    </div>
                    <div class="casilla">
                        <label>Direccion: </label>
                        <input type="text" name="direccion">
                    </div>
                    <div class="casilla">
                        <label>Tipo membresia: </label>
                        <input type="text" name="tipo_membresia">
                    </div>
                </div>
                
                <div class="administrador">
                    <h2>Administrador</h2>
                    <div class="casilla">
                        <label>Nombre: </label>
                        <input type="text" name="nombre_admin">
                    </div>
                    <div class="casilla">
                        <label>Apellido: </label>
                        <input type="text" name="apellido_admin">
                    </div>
                    <div class="casilla">
                        <label>Usuario: </label>
                        <input type="text" name="usuario_admin">
                    </div>
                    <div class="casilla">
                        <label>Clave</label>
                        <input type="password" name="clave_admin">
                    </div>
                </div>
            </div>
            <button class="boton restaurante_crear" ype="submit">Crear Restaurante y Administrador</button>
        </form>
    </div>

</body>
</html>
