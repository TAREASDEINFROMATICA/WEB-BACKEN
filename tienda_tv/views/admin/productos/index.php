<?php
$title = "Gestión de Productos - Admin";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-tv"></i> Gestión de Productos</h1>
            <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=crear" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </div>
        <p class="lead">Administra los productos de la tienda</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Lista de Productos
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($productos)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay productos registrados.
                    </div>
                <?php else: ?>
                    <?php
                    $productos_unicos = [];
                    $ids_vistos = [];

                    foreach ($productos as $producto) {
                        $id = $producto['id_producto'];
                        if (!in_array($id, $ids_vistos)) {
                            $productos_unicos[] = $producto;
                            $ids_vistos[] = $id;
                        }
                    }
                    $productos = $productos_unicos;
                    ?>

                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Mostrando <strong><?php echo count($productos); ?> productos únicos</strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Marca</th>
                                    <th>Categoría</th>
                                    <!-- <th>Stock</th>-->
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><strong><?php echo $producto['id_producto']; ?></strong></td>
                                        <td>
                                            <?php if ($producto['imagen_url']): ?>
                                                <img src="<?php echo $producto['imagen_url']; ?>" alt="<?php echo $producto['nombre']; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-tv text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo $producto['nombre']; ?></strong>
                                            <br>
                                            <small class="text-muted">SKU: <?php echo $producto['codigo_sku']; ?></small>
                                        </td>
                                        <td>
                                            <strong class="text-success">$<?php echo number_format($producto['precio'], 2); ?></strong>
                                        </td>
                                        <td><?php echo $producto['nombre_marca'] ?: 'N/A'; ?></td>
                                        <td><?php echo $producto['nombre_categoria'] ?: 'N/A'; ?></td>
                                        <!--  <td>
                                            <span class="badge bg-<?php echo $producto['stock_actual'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $producto['stock_actual']; ?> unidades
                                            </span>
                                        </td> -->
                                        <td>
                                            <span class="badge bg-<?php echo $producto['disponible'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $producto['disponible'] ? 'Disponible' : 'Sin Stock'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=detalle&id=<?php echo $producto['id_producto']; ?>"
                                                    class="btn btn-outline-info" title="Ver detalle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=editar&id=<?php echo $producto['id_producto']; ?>"
                                                    class="btn btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=eliminar&id=<?php echo $producto['id_producto']; ?>"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar este producto?')"
                                                    title="Eliminar">
                                                    <i class="fas fa-trash"></i>
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