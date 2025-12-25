<?php
$title = "Detalles - " . htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8');
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index">
                        <i class="fas fa-tv"></i> Catálogo
                    </a>
                </li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <?php if (!empty($producto['imagen_url'])): ?>
                    <img src="<?php echo htmlspecialchars($producto['imagen_url'], ENT_QUOTES, 'UTF-8'); ?>"
                        class="img-fluid rounded"
                        alt="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                        style="max-height: 400px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                        style="height: 400px;">
                        <i class="fas fa-tv fa-5x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0"><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-primary"><?php echo htmlspecialchars($producto['nombre_marca'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($producto['nombre_categoria'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php if (!empty($producto['smart_tv'])): ?>
                        <span class="badge bg-success">Smart TV</span>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <h5>Descripción</h5>
                    <p class="text-muted">
                        <?php echo !empty($producto['descripcion']) ? htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8') : 'Sin descripción disponible.'; ?>
                    </p>
                </div>
                <div class="mb-4">
                    <h5>Especificaciones Técnicas</h5>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">
                                <i class="fas fa-expand"></i> <strong>Tamaño:</strong><br>
                                <?php echo (int)$producto['pulgadas']; ?> pulgadas
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">
                                <i class="fas fa-hdmi"></i> <strong>Resolución:</strong><br>
                                <?php echo htmlspecialchars($producto['resolucion'], ENT_QUOTES, 'UTF-8'); ?>
                            </small>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <small class="text-muted">
                                <i class="fas fa-plug"></i> <strong>Puertos HDMI:</strong><br>
                                <?php echo (int)$producto['hdmi_puertos']; ?> puertos
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">
                                <i class="fas fa-usb"></i> <strong>Puertos USB:</strong><br>
                                <?php echo (int)$producto['usb_puertos']; ?> puertos
                            </small>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> <strong>Garantía:</strong><br>
                                <?php echo (int)$producto['garantia_meses']; ?> meses
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">
                                <i class="fas fa-barcode"></i> <strong>SKU:</strong><br>
                                <?php echo htmlspecialchars($producto['codigo_sku'], ENT_QUOTES, 'UTF-8'); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-primary mb-0">$<?php echo number_format($producto['precio'], 2); ?></h2>
                            <small class="text-muted">Precio final</small>
                        </div>
                        <div class="text-end">
                            <?php if (!empty($producto['disponible'])): ?>
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check"></i> En Stock
                                </span><br>
                                <small class="text-muted">
                                    <?php echo (int)$producto['stock_actual']; ?> unidades disponibles
                                </small>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times"></i> Agotado
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMINISTRADOR'): ?>
                       
                    <?php elseif (!empty($producto['disponible'])): ?>
                        <button type="button"
                            class="btn btn-success btn-lg w-100 btn-agregar-carrito-detalle"
                            data-producto-id="<?php echo (int)$producto['id_producto']; ?>"
                            data-producto-nombre="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-producto-precio="<?php echo number_format($producto['precio'], 2, '.', ''); ?>"
                            data-producto-imagen="<?php echo htmlspecialchars($producto['imagen_url'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-producto-stock="<?php echo (int)$producto['stock_actual']; ?>">
                            <i class="fas fa-cart-plus"></i> Agregar al Carrito
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="fas fa-times"></i> Producto No Disponible
                        </button>
                    <?php endif; ?>

                    <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Catálogo
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row mt-5">
    <div class="col-12">
        <h4>También te podría interesar</h4>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-tv fa-2x text-muted"></i>
                        <p class="mt-2 mb-0">Más productos de <?php echo htmlspecialchars($producto['nombre_marca'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index" class="btn btn-sm btn-outline-primary mt-2">
                            Ver Catálogo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo HTTP_BASE; ?>/public/js/productos.js"></script>
<?php
include __DIR__ . '/../../layouts/footer.php';
?>