<?php

session_start();

// Si ya hay una sesión activa, lo mandamos a su panel
if (isset($_SESSION['ruta_inicio'])) {
    header("Location:" . $_SESSION['ruta_inicio']);
    exit;
}

// Si no hay sesión, mostramos el formulario
include("/views/auth/form_inicio.php");
?>

<?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
    <div class="alerta-error">
        ⚠️ Usuario, contraseña o restaurante incorrectos.
    </div>
<?php endif; ?> 
