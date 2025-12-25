<?php
$title = "Ajustar Stock - " . $producto['nombre'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-edit"></i> Ajustar Stock
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6>Información del Producto:</h6>
                    <strong>Nombre:</strong> <?php echo $producto['nombre']; ?><br>
                    <strong>SKU:</strong> <?php echo $producto['codigo_sku']; ?><br>
                    <strong>Stock Actual:</strong>
                    <span class="badge bg-<?php
                                            if ($inventario['stock_actual'] == 0) echo 'danger';
                                            elseif ($inventario['stock_actual'] <= $inventario['stock_minimo']) echo 'warning';
                                            else echo 'success';
                                            ?>">
                        <?php echo $inventario['stock_actual']; ?> unidades
                    </span><br>
                    <strong>Stock Mínimo:</strong> <?php echo $inventario['stock_minimo']; ?> unidades
                </div>

                <form action="<?php echo HTTP_BASE; ?>/?c=InventarioController&a=ajustar&id=<?php echo $producto['id_producto']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Ajuste *</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="agregar">Agregar Stock</option>
                            <option value="reducir">Reducir Stock</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad *</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad"
                            min="1" required placeholder="Ingrese la cantidad">
                        <div class="form-text">Cantidad de unidades a agregar o reducir</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo HTTP_BASE; ?>/?c=InventarioController&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Aplicar Ajuste
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!--
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Instrucciones</h5>
                <div class="alert alert-warning">
                    <small>
                        <strong><i class="fas fa-info-circle"></i> Agregar Stock:</strong><br>
                        • Aumenta el inventario disponible<br>
                        • Use para ingresar nuevas unidades<br><br>

                        <strong><i class="fas fa-info-circle"></i> Reducir Stock:</strong><br>
                        • Disminuye el inventario disponible<br>
                        • Use para corregir errores o mermas<br>
                        • No puede reducir por debajo de 0
                    </small>
                </div>
                <div class="mt-3">
                    <h6>Previsualización:</h6>
                    <p id="preview" class="text-muted">
                        Stock actual: <strong><?php echo $inventario['stock_actual']; ?></strong> unidades
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
-->
<script src="<?php echo HTTP_BASE; ?>/public/js/ajuste_inventario.js"></script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>