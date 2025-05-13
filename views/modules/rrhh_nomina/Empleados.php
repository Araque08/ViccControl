<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Empleado</title>
    <link rel="stylesheet" href="/proyecto/public/css/admin.css"> <!-- Opcional, si tienes estilos -->
</head>
<body>
<h2>Registrar Nuevo Empleado</h2>
<form action="/proyecto/controller/admin/guardar_empleado.php" method="POST">
    <label>Nombre:</label>
    <input type="text" name="nombre" required><br>

    <label>Apellido:</label>
    <input type="text" name="apellido" required><br>

    <label>Lugar de nacimiento:</label>
    <input type="text" name="lugar_nacimiento"><br>

    <label>Fecha de nacimiento:</label>
    <input type="date" name="fecha_nacimiento"><br>

    <label>Estado civil:</label>
    <select name="estado_civil">
        <option value="">--Seleccionar--</option>
        <option value="Soltero">Soltero</option>
        <option value="Casado">Casado</option>
        <option value="Divorciado">Divorciado</option>
        <option value="Viudo">Viudo</option>
    </select><br>

    <label>Dirección:</label>
    <input type="text" name="direccion"><br>

    <label>Teléfono:</label>
    <input type="text" name="telefono"><br>

    <label>Email:</label>
    <input type="email" name="email"><br>

    <label>Cuenta bancaria:</label>
    <input type="text" name="cuenta_banco"><br>

    <label>Salario:</label>
    <input type="number" step="0.01" name="salario"><br>

    <label>Número de documento:</label>
    <input type="text" name="numero_documento"><br>

    <label>Funciones:</label>
    <input type="text" name="funciones"><br>

    <label>¿Tiene hijos?</label>
    <select name="tiene_hijos">
        <option value="0">No</option>
        <option value="1">Sí</option>
    </select><br>

    <label>Cantidad de hijos:</label>
    <input type="number" name="cantidad_hijos" min="0"><br>

    <label>Cargo:</label>
    <select name="fk_id_cargo" required>
        <!-- Puedes llenar esto dinámicamente con PHP si tienes tabla de cargos -->
        <option value="1">Mesero</option>
        <option value="2">Cocinero</option>
        <option value="3">Gerente</option>
    </select><br>

    <label>Tipo de Contrato:</label>
    <select name="fk_id_contrato" required>
        <!-- Puedes llenar esto dinámicamente también -->
        <option value="1">Término fijo</option>
        <option value="2">Término indefinido</option>
    </select><br>

    <h3>Crear Usuario para el Empleado</h3>
    <label>Nombre de Usuario:</label>
    <input type="text" name="usuario" required><br>

    <label>Contraseña:</label>
    <input type="password" name="contrasena" required><br>

    <label>Rol:</label>
    <select name="id_rol" required>
        <!-- Llenar con los roles disponibles -->
        <option value="3">Cajero</option>
        <option value="4">Mesero</option>
    </select><br>

    <button type="submit">Registrar Empleado</button>
</form>
</body>
</html>
