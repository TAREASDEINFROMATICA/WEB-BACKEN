<?php
if (!defined('HTTP_BASE')) {
    define('HTTP_BASE', 'http://localhost/tienda_tv');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Tienda de TVs'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo HTTP_BASE; ?>/public/css/header.css">
    <style>
        /* Reset mínimo */
        body {
            margin: 0;
            padding: 0;
        }
        
        /* Contenedor principal */
        .main-container {
            min-height: calc(100vh - 200px);
        }
        
        /* Badge del carrito */
        .carrito-badge {
            position: relative;
            top: -2px;
        }
    </style>
</head>

<body class="<?php echo isset($_SESSION['usuario']) ? 'usuario-logueado' : 'usuario-no-logueado'; ?>">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo HTTP_BASE; ?>">
                <i class="fas fa-tv"></i> Tienda de TVs
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <?php if ($_SESSION['usuario']['rol'] == 'CLIENTE'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index">
                                    <i class="fas fa-tv"></i> Catálogo
                                </a>
                            </li>
                        <?php elseif ($_SESSION['usuario']['rol'] == 'ADMINISTRADOR'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo HTTP_BASE; ?>/?c=AdminController&a=dashboard">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo HTTP_BASE; ?>/?c=ProveedoresController&a=index">
                                    <i class="fas fa-truck"></i> Proveedores
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index">
                                <i class="fas fa-tv"></i> Catálogo
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <?php if ($_SESSION['usuario']['rol'] == 'CLIENTE'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo HTTP_BASE; ?>/?c=CarritoController&a=index">
                                    <i class="fas fa-shopping-cart"></i> Carrito
                                    <span id="carrito-contador" class="badge bg-danger carrito-badge">
                                        <?php 
                                        if(isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] == 'CLIENTE') {
                                            require_once ROOT_CORE . '/../models/CarritoModel.php';
                                            $carritoModel = new CarritoModel();
                                            $count = $carritoModel->getCountByUser($_SESSION['usuario']['id_usuario']);
                                            echo $count > 0 ? $count : '';
                                        }
                                        ?>
                                    </span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=index">
                                    <i class="fas fa-receipt"></i> Mis Compras
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <span class="dropdown-item-text">
                                        <small class="text-muted">Conectado como:</small><br>
                                        <strong><?php echo htmlspecialchars($_SESSION['usuario']['rol']); ?></strong>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo HTTP_BASE; ?>/?c=UsuariosController&a=perfil&id=<?php echo $_SESSION['usuario']['id_usuario']; ?>">
                                        <i class="fas fa-user-edit"></i> Editar Perfil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo HTTP_BASE; ?>/?c=Auth&a=logout">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </li>

                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo HTTP_BASE; ?>/?c=Auth&a=login">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo HTTP_BASE; ?>/?c=Auth&a=register">
                                <i class="fas fa-user-plus"></i> Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3 main-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>