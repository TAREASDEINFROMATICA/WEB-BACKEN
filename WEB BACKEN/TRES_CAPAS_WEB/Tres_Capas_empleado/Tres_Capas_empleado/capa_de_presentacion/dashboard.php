<?php
require_once "../capa_de_negocio/dashboardController.php"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="../capa_de_presentacion/public/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="theme-gradient">
  <div class="topbar">
    <div class="topbar-inner">
      <h2 class="page-title">Bienvenido, <?php echo htmlspecialchars($nombre_completo); ?></h2>
      <div class="spacer"></div>
      <div class="clock" id="clock"><?php echo $hora_servidor; ?></div>
    </div>
  </div>

  <div class="container">
    <div class="meta-container">
      <div class="card">
        <div class="card-icon"></div>
        <div class="card-label">Usuario</div>
        <div class="card-value"><?php echo htmlspecialchars($nombre_completo); ?></div>
      </div>

      <div class="card">
        <div class="card-icon"></div>
        <div class="card-label">Turno</div>
        <div class="card-value"><?php echo htmlspecialchars($turno); ?></div>
      </div>

      <div class="card">
        <div class="card-icon"></div>
        <div class="card-label">Estado</div>
        <div class="card-value">
          <span class="badge <?php echo $estado_cls; ?>"><?php echo $estado_txt; ?></span>
        </div>
      </div>

      <div class="card">
        <div class="card-icon"></div>
        <div class="card-label">Fecha</div>
        <div class="card-value"><?php echo $fecha_hoy; ?></div>
      </div>
    </div>

    <div class="actions-grid">
      <a class="btn" href="../capa_de_presentacion/marcarEntrada.php">Marcar Entrada</a>
      <a class="btn" href="../capa_de_presentacion/marcarSalida.php">Marcar Salida</a>
      <a class="btn" href="../capa_de_presentacion/reportePagos.php">Ver Pagos</a>
      <a class="btn btn-out" href="../capa_de_presentacion/login.php">Salir</a>
    </div>
  </div>

  <script src="../capa_de_presentacion/public/js/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
