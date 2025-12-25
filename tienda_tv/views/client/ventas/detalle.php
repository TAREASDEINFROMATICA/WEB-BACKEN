<?php
$title = "Detalles de Compra #" . $venta['id_venta'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-shopping-bag"></i> Detalles de Compra</h1>
            <a href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <p class="lead">Factura #<?php echo str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-boxes"></i> Productos Comprados
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($detalles)): ?>
                    <div class="alert alert-warning">No se encontraron productos en esta compra.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $detalle): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $detalle['producto_nombre']; ?></strong>
                                        </td>
                                        <td><?php echo $detalle['cantidad']; ?></td>
                                        <td>$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                        <td><strong>$<?php echo number_format($detalle['subtotal'], 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-receipt"></i> Resumen de Compra
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Fecha:</strong><br>
                    <?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?>
                </div>

                <div class="mb-3">
                    <strong>Método de Pago:</strong><br>
                    <span class="badge bg-info"><?php echo $venta['metodo_pago']; ?></span>
                </div>

                <div class="mb-3">
                    <strong>Estado:</strong><br>
                    <span class="badge bg-<?php echo $venta['estado_pago'] == 'PAGADO' ? 'success' : 'warning'; ?>">
                        <?php echo $venta['estado_pago']; ?>
                    </span>
                </div>

                <hr>

                <div class="row">
                    <div class="col-6">Subtotal:</div>
                    <div class="col-6 text-end">$<?php echo number_format($venta['subtotal'], 2); ?></div>

                    <div class="col-6">Descuento:</div>
                    <div class="col-6 text-end">$<?php echo number_format($venta['descuento'], 2); ?></div>

                    <div class="col-6">Impuesto:</div>
                    <div class="col-6 text-end">$<?php echo number_format($venta['impuesto'], 2); ?></div>

                    <div class="col-6"><strong>Total:</strong></div>
                    <div class="col-6 text-end"><strong class="text-success">$<?php echo number_format($venta['total'], 2); ?></strong></div>
                </div>

                <div class="d-grid mt-3">
                    <a href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=factura&id=<?php echo $venta['id_venta']; ?>"
                        class="btn btn-success">
                        <i class="fas fa-print"></i> Ver Factura Completa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>