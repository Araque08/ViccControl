<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="/..//public/css/logIn.css">
</head>
<body>
<?php if (isset($_GET['expirada']) && $_GET['expirada'] == 1): ?>
    <div class="alerta">⚠️ Tu sesión ha expirado por inactividad.</div>
<?php endif; ?>
    <div class="container">
        <div class="login-box">
            <div class="logo">
                <img src="/../public/img/ViccControlImg.png" alt="Logo VICControl">
            </div>
            <form action="/../controller/auth/logIn.php" method="POST">
                <div class="input-group">
                    <i class="fas fa-home"></i>
                    <input type="text" id="compania" name="compania" placeholder="Compañía" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="usuario" name="usuario" placeholder="Usuario" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>
            <div class="footer">
                <p>Copyright &copy; 2025-ViccControl</p>
            </div>
        </div>
    </div>
</body>
</html>
