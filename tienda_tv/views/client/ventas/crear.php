<?php
$title = "Procesar Compra";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1><i class="fas fa-credit-card"></i> Procesar Compra</h1>
        <p class="lead">Confirma tu pedido y selecciona método de pago</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-bag"></i> Resumen de tu Pedido
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($carrito)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Tu carrito está vacío.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subtotal = 0;
                                $total_productos = 0;
                                ?>
                                <?php foreach ($carrito as $item): ?>
                                    <?php
                                    $item_subtotal = $item['precio'] * $item['cantidad'];
                                    $subtotal += $item_subtotal;
                                    $total_productos += $item['cantidad'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($item['imagen_url'])): ?>
                                                    <img src="<?php echo $item['imagen_url']; ?>"
                                                        class="me-3 rounded"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light me-3 d-flex align-items-center justify-content-center rounded"
                                                        style="width: 50px; height: 50px;">
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
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                        <td><?php echo $item['cantidad']; ?></td>
                                        <td><strong>$<?php echo number_format($item_subtotal, 2); ?></strong></td>
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
                    <i class="fas fa-file-invoice-dollar"></i> Total del Pedido
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Productos:</strong> <?php echo $total_productos; ?> items<br>
                    <strong>Subtotal:</strong> $<?php echo number_format($subtotal, 2); ?><br>
                    <strong>Descuento:</strong> $0.00<br>
                    <strong>Impuesto (13%):</strong> $<?php echo number_format($subtotal * 0.13, 2); ?><br>
                    <hr>
                    <h4 class="text-success">
                        <strong>Total: $<?php echo number_format($subtotal * 1.13, 2); ?></strong>
                    </h4>
                </div>

                <form action="<?php echo HTTP_BASE; ?>/?c=VentasController&a=crear" method="POST">
                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label"><strong>Método de Pago *</strong></label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                            <option value="">Seleccionar método...</option>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TARJETA">Tarjeta de Crédito/Débito</option>
                            <option value="QR">QR</option>
                            <option value="TRANSFERENCIA">Transferencia Bancaria</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="direccion_entrega" class="form-label"><strong>Dirección de Entrega</strong></label>
                        <textarea class="form-control" id="direccion_entrega" name="direccion_entrega"
                            rows="3" placeholder="Ingresa tu dirección de entrega..."><?php echo $_SESSION['usuario']['direccion'] ?? ''; ?></textarea>
                        <div class="form-text">
                            Si no especificas una dirección, usaremos la de tu perfil.
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i> Confirmar Compra
                        </button>
                        <a href="<?php echo HTTP_BASE; ?>/?c=CarritoController&a=index" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Carrito
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <!--
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="fas fa-info-circle"></i> Información Importante</h6>
                <small class="text-muted">
                    • Tu pedido será procesado inmediatamente<br>
                    • Recibirás un correo con los detalles<br>
                    • Tiempo de entrega: 3-5 días hábiles<br>
                    • Para consultas: +591 12345678
                </small>
            </div>
        </div>
        -->
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>