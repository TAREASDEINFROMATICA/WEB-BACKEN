<?php
$title = "Inicio - Tienda de TVs";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo HTTP_BASE; ?>/public/css/home.css">
</head>

<body>
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">
                <span class="icon-tv"></span>Bienvenido a nuestra Tienda de TVs
            </h1>

            <p class="hero-subtitle">
                Encuentra los mejores televisores con la mejor calidad y precio del mercado
            </p>

            <div class="btn-center">
                <a href="<?php echo HTTP_BASE; ?>/?c=Auth&a=catalogo" class="btn btn-primary">
                    <span class="icon-login"></span>Ver Catálogo Completo
                </a>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?php echo HTTP_BASE; ?>';
    </script>
    <script src="<?php echo HTTP_BASE; ?>/public/js/main.js"></script> 
</body>
</html>
