<?php
$title = "Mis Compras";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1><i class="fas fa-receipt"></i> Mis Compras</h1>
        <p class="lead">Historial de todas tus compras realizadas</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?php if (empty($ventas)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> Aún no has realizado ninguna compra.
                <br>
                <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index" class="btn btn-primary mt-2">
                    <i class="fas fa-tv"></i> Ir al Catálogo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th># Factura</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Método de Pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td><strong>#<?php echo str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></td>
                                <td><strong class="text-success">$<?php echo number_format($venta['total'], 2); ?></strong></td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="fas fa-credit-card"></i>
                                        <?php echo $venta['metodo_pago']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $venta['estado_pago'] == 'PAGADO' ? 'success' : 'warning'; ?>">
                                        <?php echo $venta['estado_pago']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=detalle&id=<?php echo $venta['id_venta']; ?>"
                                            class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i> Detalles
                                        </a>
                                        <a href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=factura&id=<?php echo $venta['id_venta']; ?>"
                                            class="btn btn-outline-success">
                                            <i class="fas fa-file-invoice"></i> Factura
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>