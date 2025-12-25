<?php
$title = "Panel de Administración";
include __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1><i class="fas fa-cog"></i> Panel de Administración</h1>
        <p class="lead">Bienvenido al centro de control de la tienda</p>
    </div>
</div>
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Productos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $stats['total_productos'] - 1; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tv fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Usuarios
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $stats['total_usuarios']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Ventas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $stats['total_ventas']; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Stock Bajo
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count($stats['stock_bajo']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt"></i> Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index" class="btn btn-primary w-100">
                            <i class="fas fa-tv"></i> Gestionar Productos
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo HTTP_BASE; ?>/?c=UsuariosController&a=index" class="btn btn-success w-100">
                            <i class="fas fa-users"></i> Gestionar Usuarios
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo HTTP_BASE; ?>/?c=ProveedoresController&a=index" class="btn btn-secondary w-100">
                            <i class="fas fa-truck"></i> Gestionar Proveedores
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=index" class="btn btn-info w-100">
                            <i class="fas fa-chart-bar"></i> Ver Ventas
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo HTTP_BASE; ?>/?c=InventarioController&a=index" class="btn btn-warning w-100">
                            <i class="fas fa-boxes"></i> Control Inventario
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo HTTP_BASE; ?>/?c=MarcasController&a=index" class="btn btn-dark w-100">
                            <i class="fas fa-tags"></i> Gestionar Marcas
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo HTTP_BASE; ?>/?c=CategoriasController&a=index" class="btn btn-light w-100 border">
                            <i class="fas fa-layer-group"></i> Gestionar Categorías
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history"></i> Últimas Ventas
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($stats['ultimas_ventas'])): ?>
                    <div class="alert alert-info">
                        No hay ventas registradas aún.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th># Venta</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['ultimas_ventas'] as $venta): ?>
                                    <tr>
                                        <td>#<?php echo $venta['id_venta']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></td>
                                        <td><strong>$<?php echo number_format($venta['total'], 2); ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?php echo $venta['estado_pago'] == 'PAGADO' ? 'success' : 'warning'; ?>">
                                                <?php echo $venta['estado_pago']; ?>
                                            </span>
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
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Stock Bajo
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($stats['stock_bajo'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Todo el stock está en niveles normales.
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($stats['stock_bajo'] as $producto): ?>
                            <a href="#" class="list-group-item list-group-item-action list-group-item-warning">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo $producto['producto_nombre']; ?></h6>
                                    <small>Stock: <?php echo $producto['stock_actual']; ?></small>
                                </div>
                                <small>Mínimo: <?php echo $producto['stock_minimo']; ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>