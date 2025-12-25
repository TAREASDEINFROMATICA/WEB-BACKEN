<?php
require_once "../capa_de_negocio/marcarEntradaController.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Marcar Entrada</title>
  <link rel="stylesheet" href="../capa_de_presentacion/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="theme-gradient">
  <div class="topbar">
    <div class="topbar-inner">
      <h2 class="page-title">Marcar Entrada</h2>
      <div class="spacer"></div>
      <div class="clock" id="clock"> <?php echo $hora_servidor; ?></div>
    </div>
  </div>

  <div class="container" style="max-width:640px">
    <?php if ($msg): ?><div class="msg <?php echo $cls; ?>"><?php echo $msg; ?></div><?php endif; ?>

    <div class="form-card">
      <div class="form-head entry">
        <div class="form-icon"></div>
        <div>
          <div class="form-title">Registrar Entrada</div>
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
            class="btn"
            type="submit"
            id="btnEntrada"
            data-ya="<?php echo $yaExiste ? '1' : '0'; ?>"
            data-hora="<?php echo $yaExiste ? htmlspecialchars(date('H:i:s', strtotime($yaExiste))) : ''; ?>"
          >Marcar Entrada</button>
          <a class="btn btn-out" href="dashboard.php">Volver</a>
        </div>
      </form>
    </div>
  </div>
  <script src="../capa_de_presentacion/public/js/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
