<?php
$title = "Gestión de Inventario";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-boxes"></i> Control de Inventario</h1>
        </div>
        <p class="lead">Gestiona el stock de productos</p>
    </div>
</div>

<!-- Alertas de Stock Bajo -->
<?php if (!empty($stockBajo)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Alertas de Stock Bajo</h5>
                <div class="row">
                    <?php foreach ($stockBajo as $producto): ?>
                        <div class="col-md-4 mb-2">
                            <div class="card border-warning">
                                <div class="card-body py-2">
                                    <h6 class="card-title mb-1"><?php echo $producto['producto_nombre']; ?></h6>
                                    <p class="card-text mb-0">
                                        <small>Stock: <strong class="text-danger"><?php echo $producto['stock_actual']; ?></strong></small>
                                        <small class="ms-2">Mínimo: <?php echo $producto['stock_minimo']; ?></small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Inventario de Productos
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($inventario)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay productos en el inventario.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Producto</th>
                                    <th>SKU</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inventario as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $item['producto_nombre']; ?></strong>
                                        </td>
                                        <td><?php echo $item['codigo_sku']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                                    if ($item['stock_actual'] == 0) echo 'danger';
                                                                    elseif ($item['stock_actual'] <= $item['stock_minimo']) echo 'warning';
                                                                    else echo 'success';
                                                                    ?> fs-6">
                                                <?php echo $item['stock_actual']; ?> unidades
                                            </span>
                                        </td>
                                        <td><?php echo $item['stock_minimo']; ?></td>
                                        <td>
                                            <?php if ($item['stock_actual'] == 0): ?>
                                                <span class="badge bg-danger">Agotado</span>
                                            <?php elseif ($item['stock_actual'] <= $item['stock_minimo']): ?>
                                                <span class="badge bg-warning">Stock Bajo</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Disponible</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo HTTP_BASE; ?>/?c=InventarioController&a=ajustar&id=<?php echo $item['id_producto']; ?>"
                                                    class="btn btn-outline-primary" title="Ajustar Stock">
                                                    <i class="fas fa-edit"></i> Ajustar
                                                </a>
                                                <a href="<?php echo HTTP_BASE; ?>/?c=InventarioController&a=eliminar&id=<?php echo $item['id_producto']; ?>" 
                                                class="btn btn-outline-danger" 
                                                onclick="return confirm('¿Estás seguro de eliminar este registro de inventario? El producto seguirá existiendo.')"
                                                title="Eliminar Inventario">
                                                    <i class="fas fa-trash"></i> Eliminar
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
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>