<?php
require_once "../capa_de_negocio/registroController.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Registro</title>
  <link rel="stylesheet" href="../capa_de_presentacion/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="theme-gradient auth-page">
  <div class="auth-card">
    <h1 class="auth-title">Registro de Usuario</h1>

    <form method="POST" class="auth-form">
      <div class="form-row">
        <div class="form-field">
          <label>Usuario</label>
          <input class="input" type="text" name="usuario" placeholder="Crea tu usuario" required autocomplete="username">
        </div>
        <div class="form-field">
          <label>Contraseña</label>
          <div class="input-with-action">
            <input class="input" type="password" name="contrasena" id="regPass" placeholder="Crea una contraseña" required autocomplete="new-password">
            <button type="button" class="ghost" id="toggleRegPass" aria-label="Mostrar u ocultar contraseña">👁️</button>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label>Nombre</label>
          <input class="input" type="text" name="nombre" placeholder="Nombre" required>
        </div>
        <div class="form-field">
          <label>Apellido Paterno</label>
          <input class="input" type="text" name="apellido_paterno" placeholder="Apellido paterno" required>
        </div>
        <div class="form-field">
          <label>Apellido Materno</label>
          <input class="input" type="text" name="apellido_materno" placeholder="Apellido materno" required>
        </div>
      </div>

      <div class="form-field">
        <label>Turno</label>
        <select class="input" name="turno" required>
          <option value="Completo">Completo</option>
          <option value="Medio Turno">Medio Turno</option>
          <option value="Fines de Semana">Fines de Semana</option>
        </select>
      </div>

      <button class="btn btn-full" type="submit">Registrar</button>
    </form>

    <p class="muted">
      <a class="link" href="../capa_de_presentacion/login.php">Volver al Login</a>
    </p>
  </div>

  <script src="../capa_de_presentacion/public/js/registro.js"></script>
</body>
</html>
