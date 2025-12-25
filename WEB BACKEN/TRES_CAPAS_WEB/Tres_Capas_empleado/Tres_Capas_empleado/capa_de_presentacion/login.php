<?php
require_once "../capa_de_negocio/loginController.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link rel="stylesheet" href="../capa_de_presentacion/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="theme-gradient auth-page">
  <div class="auth-card">
    <h1 class="auth-title">Iniciar Sesión</h1>

    <?php if (!empty($error)): ?>
      <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
      <div class="form-field">
        <label>Usuario</label>
        <input class="input" type="text" name="usuario" placeholder="Tu usuario" required autofocus autocomplete="username">
      </div>

      <div class="form-field">
        <label>Contraseña</label>
        <div class="input-with-action">
          <input class="input" type="password" name="contrasena" id="loginPass" placeholder="Tu contraseña" required autocomplete="current-password">
          <button type="button" class="ghost" id="toggleLoginPass" aria-label="Mostrar u ocultar contraseña">👁️</button>
        </div>
      </div>

      <button class="btn btn-full" type="submit">Ingresar</button>
    </form>

    <p class="muted">
      ¿No tienes cuenta?
      <a class="link" href="registro.php">Regístrate</a>
    </p>
  </div>

  <script src="../capa_de_presentacion/public/js/login.js"></script>
</body>
</html>
