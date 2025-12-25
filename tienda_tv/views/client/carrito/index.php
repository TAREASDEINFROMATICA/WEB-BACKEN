<?php
$title = "Carrito de Compras";
include __DIR__ . '/../../layouts/header.php';

// Determinar si estamos mostrando carrito de BD o temporal
$esCarritoTemporal = !isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'CLIENTE';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>
        <p class="lead">Revisa y gestiona los productos en tu carrito</p>
        
        <?php if ($esCarritoTemporal): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Carrito Temporal:</strong> Tus productos están guardados localmente. 
                <a href="<?php echo HTTP_BASE; ?>/?c=Auth&a=login" class="alert-link">Inicia sesión</a> 
                para guardarlos permanentemente y proceder al pago.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Contenedor para carrito de BD -->
        <div id="carrito-bd" style="<?php echo $esCarritoTemporal ? 'display: none;' : ''; ?>">
            <?php if (empty($carrito) && !$esCarritoTemporal): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Tu carrito está vacío.
                    <br>
                    <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index" class="btn btn-primary mt-2">
                        <i class="fas fa-tv"></i> Ir al Catálogo
                    </a>
                </div>
            <?php elseif (!$esCarritoTemporal && !empty($carrito)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; ?>
                            <?php foreach ($carrito as $item): ?>
                                <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                                <?php $total += $subtotal; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['imagen_url'])): ?>
                                                <img src="<?php echo $item['imagen_url']; ?>"
                                                    class="me-3 rounded"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light me-3 d-flex align-items-center justify-content-center rounded"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-tv text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo $item['nombre']; ?></strong><br>
                                                <small class="text-muted">
                                                    <?php if (isset($item['pulgadas']) && $item['pulgadas']): ?>
                                                        <?php echo $item['pulgadas']; ?> pulgadas
                                                    <?php endif; ?>
                                                    <?php if (isset($item['resolucion']) && $item['resolucion']): ?>
                                                        • <?php echo $item['resolucion']; ?>
                                                    <?php endif; ?>
                                                    <?php if (isset($item['smart_tv']) && $item['smart_tv']): ?>
                                                        • Smart TV
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                    <td>
                                        <div class="input-group" style="width: 120px;">
                                            <input type="number"
                                                class="form-control quantity-input"
                                                value="<?php echo $item['cantidad']; ?>"
                                                min="1"
                                                max="<?php echo $item['stock_actual'] ?? 99; ?>"
                                                data-carrito-id="<?php echo $item['id_carrito']; ?>">
                                        </div>
                                    </td>
                                    <td><strong>$<?php echo number_format($subtotal, 2); ?></strong></td>
                                    <td>
                                        <a href="<?php echo HTTP_BASE; ?>/?c=CarritoController&a=eliminar&id=<?php echo $item['id_carrito']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Eliminar este producto del carrito?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong class="text-success">$<?php echo number_format($total, 2); ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contenedor para carrito temporal (LocalStorage) -->
        <div id="carrito-temporal" style="<?php echo !$esCarritoTemporal ? 'display: none;' : ''; ?>">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="carrito-temporal-body">
                        <!-- Los productos del localStorage se cargarán aquí con JavaScript -->
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Temporal:</strong></td>
                            <td><strong class="text-success" id="total-temporal">$0.00</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Mensaje cuando ambos carritos están vacíos -->
        <div id="carrito-vacio" class="alert alert-info text-center" style="display: none;">
            <i class="fas fa-info-circle"></i> Tu carrito está vacío.
            <br>
            <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index" class="btn btn-primary mt-2">
                <i class="fas fa-tv"></i> Ir al Catálogo
            </a>
        </div>

        <div id="acciones-carrito" class="d-flex justify-content-between mt-4" style="<?php echo ($esCarritoTemporal || empty($carrito)) ? 'display: none;' : ''; ?>">
            <?php if (!$esCarritoTemporal): ?>
                <a href="<?php echo HTTP_BASE; ?>/?c=CarritoController&a=limpiar"
                    class="btn btn-outline-danger"
                    onclick="return confirm('¿Vaciar todo el carrito?')">
                    <i class="fas fa-trash"></i> Vaciar Carrito
                </a>
            <?php else: ?>
                <button id="vaciar-temporal" class="btn btn-outline-danger">
                    <i class="fas fa-trash"></i> Vaciar Carrito Temporal
                </button>
            <?php endif; ?>

            <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index"
                class="btn btn-outline-primary">
                <i class="fas fa-tv"></i> Seguir Comprando
            </a>

            <?php if (!$esCarritoTemporal && !empty($carrito)): ?>
                <a href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=crear"
                    class="btn btn-success btn-lg">
                    <i class="fas fa-credit-card"></i> Proceder al Pago
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const ES_CARRITO_TEMPORAL = <?php echo $esCarritoTemporal ? 'true' : 'false'; ?>;
    const BASE_URL = '<?php echo HTTP_BASE; ?>';
</script>

<script src="<?php echo HTTP_BASE; ?>/public/js/carrito.js"></script>


<?php include __DIR__ . '/../../layouts/footer.php'; ?>