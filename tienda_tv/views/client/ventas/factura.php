<?php
$title = "Factura #" . $venta['id_venta'];
include __DIR__ . '/../../layouts/header.php';
?>

<link rel="stylesheet" href="<?php echo HTTP_BASE; ?>/public/css/factura.css">

<div class="row mb-4 no-print">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-file-invoice"></i> Factura de Compra</h1>
            <div>

                <a href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <p class="lead">Factura #<?php echo str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT); ?></p>
    </div>
</div>

<div class="factura">
    <div class="factura-header">
        <div class="row">
            <div class="col-6">
                <h2>TIENDA DE TVs</h2>
                <p class="mb-1">
                    <strong>RUC:</strong> 12345678901<br>
                    <strong>Dirección:</strong> Av. Principal #123, Ciudad<br>
                    <strong>Teléfono:</strong> +591 12345678<br>
                    <strong>Email:</strong> info@tiendatv.com
                </p>
            </div>
            <div class="col-6 text-end">
                <h3>FACTURA</h3>
                <p class="mb-1">
                    <strong>N°:</strong> <?php echo str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT); ?><br>
                    <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($venta['fecha'])); ?><br>
                    <strong>Hora:</strong> <?php echo date('H:i', strtotime($venta['fecha'])); ?><br>
                    <strong>Estado:</strong>
                    <span class="badge bg-success"><?php echo $venta['estado_pago']; ?></span>
                </p>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <h5>INFORMACIÓN DEL CLIENTE</h5>
            <p class="mb-1">
                <strong>Nombre:</strong> <?php echo $_SESSION['usuario']['nombre']; ?><br>
                <strong>Email:</strong> <?php echo $_SESSION['usuario']['correo']; ?><br>
                <strong>Teléfono:</strong> <?php echo $_SESSION['usuario']['telefono'] ?? 'No especificado'; ?><br>
                <strong>Dirección:</strong> <?php echo $_SESSION['usuario']['direccion'] ?? 'No especificada'; ?>
            </p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <h5>DETALLES DE LA COMPRA</h5>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">#</th>
                        <th width="45%">DESCRIPCIÓN</th>
                        <th width="15%" class="text-center">CANTIDAD</th>
                        <th width="15%" class="text-end">PRECIO UNIT.</th>
                        <th width="20%" class="text-end">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $contador = 1;
                    $total_general = 0;
                    ?>
                    <?php foreach ($detalles as $detalle): ?>
                        <?php
                        $subtotal = $detalle['precio_unitario'] * $detalle['cantidad'];
                        $total_general += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo $contador++; ?></td>
                            <td>
                                <strong><?php echo $detalle['producto_nombre']; ?></strong><br>
                                <small class="text-muted">Código: PROD-<?php echo $detalle['id_producto']; ?></small>
                            </td>
                            <td class="text-center"><?php echo $detalle['cantidad']; ?></td>
                            <td class="text-end">$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                            <td class="text-end"><strong>$<?php echo number_format($subtotal, 2); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-6 offset-6">
            <div class="total-box">
                <div class="row">
                    <div class="col-6"><strong>Subtotal:</strong></div>
                    <div class="col-6 text-end">$<?php echo number_format($venta['subtotal'], 2); ?></div>

                    <div class="col-6"><strong>Descuento:</strong></div>
                    <div class="col-6 text-end">$<?php echo number_format($venta['descuento'], 2); ?></div>
                    
                    <div class="col-6"><strong>Impuesto (13%):</strong></div>
                    <div class="col-6 text-end">$<?php echo number_format($venta['impuesto'], 2); ?></div>

                    <div class="col-6">
                        <h5 class="mb-0">TOTAL:</h5>
                    </div>
                    <div class="col-6 text-end">
                        <h5 class="mb-0 text-success">$<?php echo number_format($venta['total'], 2); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="factura-footer">
        <div class="row">
            <div class="col-6">
                <h6>INFORMACIÓN DE PAGO</h6>
                <p class="mb-1">
                    <strong>Método de Pago:</strong> <?php echo $venta['metodo_pago']; ?><br>
                    <strong>Fecha de Pago:</strong> <?php echo date('d/m/Y', strtotime($venta['fecha'])); ?><br>
                    <strong>N° de Transacción:</strong> TRANS-<?php echo str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT); ?>
                </p>
            </div>
            <div class="col-6">
                <h6>INFORMACIÓN ADICIONAL</h6>
                <p class="mb-1">
                    <strong>Garantía:</strong> 12 meses<br>
                    <strong>Tiempo de entrega:</strong> 3-5 días hábiles<br>
                    <strong>Soporte:</strong> soporte@tiendatv.com
                </p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 text-center">
                <p class="mb-0 text-muted">
                    <strong>¡Gracias por su compra!</strong><br>
                    Para consultas o soporte técnico, contacte a: +591 12345678
                </p>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4 no-print">
    <div class="col-12 text-center">

        <a href="<?php echo HTTP_BASE; ?>/?c=VentasController&a=index" class="btn btn-primary me-2">
            <i class="fas fa-list"></i> COMPRAS REALIZADAS
        </a>
    </div>
</div>


<?php include __DIR__ . '/../../layouts/footer.php'; ?>