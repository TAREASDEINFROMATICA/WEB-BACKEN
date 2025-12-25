<?php
$title = "Catálogo de TVs";

include __DIR__ . '/../../layouts/header.php';

// Verificar que $productos existe y es un array
if (!isset($productos) || !is_array($productos)) {
    $productos = [];
}

$productos_unicos = [];
$ids_vistos = [];

foreach ($productos as $producto) {
    $id = $producto['id_producto'] ?? null;
    if ($id !== null && !in_array($id, $ids_vistos, true)) {
        $productos_unicos[] = $producto;
        $ids_vistos[] = $id;
    }
}

$productos = $productos_unicos;
?>
<div class="row mb-4">
    <div class="col-12">
        <h1><i class="fas fa-tv"></i> Catálogo de Televisores</h1>
        <p class="lead">Encuentra el TV perfecto para tu hogar</p>
    </div>
</div>

<div class="row">
    <?php if (empty($productos)): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay productos disponibles en este momento.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($productos as $producto): ?>
            <?php
                $id          = (int)($producto['id_producto'] ?? 0);
                $nombre      = htmlspecialchars($producto['nombre'] ?? 'Producto', ENT_QUOTES, 'UTF-8');
                $marca       = htmlspecialchars($producto['nombre_marca'] ?? 'Marca', ENT_QUOTES, 'UTF-8');
                $img         = $producto['imagen_url'] ?? '';
                $pulgadas    = htmlspecialchars((string)($producto['pulgadas'] ?? ''), ENT_QUOTES, 'UTF-8');
                $resolucion  = htmlspecialchars($producto['resolucion'] ?? '', ENT_QUOTES, 'UTF-8');
                $smart       = !empty($producto['smart_tv']);
                $hdmi        = (int)($producto['hdmi_puertos'] ?? 0);
                $usb         = (int)($producto['usb_puertos'] ?? 0);
                $precio      = (float)($producto['precio'] ?? 0);
                $stock       = (int)($producto['stock_actual'] ?? 0);
                $disponible  = !empty($producto['disponible']);
            ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="card product-card h-100 shadow-sm">
                    <div class="position-relative">
                        <?php if (!empty($img)): ?>
                            <img src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>"
                                 class="card-img-top"
                                 alt="<?php echo $nombre; ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                 style="height: 200px;">
                                <i class="fas fa-tv fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="position-absolute top-0 end-0 m-2">
                            <?php if ($disponible): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> En Stock
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger">
                                    <i class="fas fa-times"></i> Agotado
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-tag"></i> <?php echo $marca; ?>
                            </small>
                        </div>
                        <h5 class="card-title"><?php echo $nombre; ?></h5>
                        <div class="specs mb-2 flex-grow-1">
                            <small class="text-muted">
                                <i class="fas fa-expand"></i> <?php echo $pulgadas; ?> pulgadas<br>
                                <i class="fas fa-hdmi"></i> <?php echo $resolucion; ?><br>
                                <?php if ($smart): ?>
                                    <i class="fas fa-wifi"></i> Smart TV<br>
                                <?php endif; ?>
                                <i class="fas fa-portrait"></i> <?php echo $hdmi; ?> HDMI • <?php echo $usb; ?> USB
                            </small>
                        </div>
                        <div class="mt-auto">
                            <h4 class="text-primary mb-2">$<?php echo number_format($precio, 2); ?></h4>
                            <small class="text-muted">
                                <i class="fas fa-box"></i>
                                <?php echo $stock; ?> unidades disponibles
                            </small>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=detalle&id=<?php echo $id; ?>"
                               class="btn btn-outline-primary btn-sm btn-ver-detalle">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>

                            <?php if ($disponible): ?>
                                <button type="button"
                                        class="btn btn-success btn-sm w-100 btn-agregar-carrito"
                                        data-producto-id="<?php echo $id; ?>">
                                    <i class="fas fa-cart-plus"></i> Agregar al Carrito
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                    <i class="fas fa-times"></i> Agotado
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="<?php echo HTTP_BASE; ?>/public/js/productos.js"></script>

<?php
include __DIR__ . '/../../layouts/footer.php';
?>
