<?php
require_once "../capa_de_negocio/marcarSalidaController.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Marcar Salida</title>
  <link rel="stylesheet" href="../capa_de_presentacion/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="theme-gradient">
  <div class="topbar">
    <div class="topbar-inner">
      <h2 class="page-title">Marcar Salida</h2>
      <div class="spacer"></div>
      <div class="clock" id="clock">⏰ <?php echo $hora_servidor; ?></div>
    </div>
  </div>

  <div class="container" style="max-width:640px">
    <?php if ($msg): ?><div class="msg <?php echo $cls; ?>"><?php echo $msg; ?></div><?php endif; ?>

    <div class="form-card">
      <div class="form-head exit">
        <div class="form-icon"></div>
        <div>
          <div class="form-title">Registrar Salida</div>
          <div class="form-subtitle">Hoy: <?php echo date("Y-m-d"); ?></div>
        </div>
      </div>

      <form method="post" class="nice-form">
        <div class="grid-2">
          <div class="form-field">
            <label>Empleado</label>
            <input type="text" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" readonly>
          </div>
          <div class="form-field">
            <label>Hora actual</label>
            <input type="text" id="horaActual" value="<?php echo $hora_servidor; ?>" readonly>
          </div>
        </div>

        <div class="form-actions">
          <button
            class="btn btn-exit"
            type="submit"
            id="btnSalida"
            data-ya="<?php echo $yaSalida ? '1' : '0'; ?>"
            data-hora="<?php echo $yaSalida ? htmlspecialchars(date('H:i:s', strtotime($yaSalida))) : ''; ?>"
            data-noentrada="<?php echo $siEntrada ? '0' : '1'; ?>"
          >Marcar Salida</button>
          <a class="btn btn-out" href="dashboard.php">Volver</a>
        </div>
      </form>
    </div>
  </div>
  <script src="../capa_de_presentacion/public/js/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
