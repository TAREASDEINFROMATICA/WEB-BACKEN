<?php
$title = "Editar Producto - " . $producto['nombre'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-edit"></i> Editar Producto
                </h4>
            </div>
            <div class="card-body">
                <form action="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=editar&id=<?php echo $producto['id_producto']; ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Producto *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo $producto['nombre']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio *</label>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio"
                                    value="<?php echo $producto['precio']; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo $producto['descripcion']; ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="id_marca" class="form-label">Marca</label>
                                <select class="form-control" id="id_marca" name="id_marca">
                                    <option value="">Seleccionar Marca</option>
                                    <?php foreach ($marcas as $marca): ?>
                                        <option value="<?php echo $marca['id_marca']; ?>"
                                            <?php echo $marca['id_marca'] == $producto['id_marca'] ? 'selected' : ''; ?>>
                                            <?php echo $marca['nombre_marca']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="id_categoria" class="form-label">Categoría</label>
                                <select class="form-control" id="id_categoria" name="id_categoria">
                                    <option value="">Seleccionar Categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?php echo $categoria['id_categoria']; ?>"
                                            <?php echo $categoria['id_categoria'] == $producto['id_categoria'] ? 'selected' : ''; ?>>
                                            <?php echo $categoria['nombre_categoria']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="id_proveedor" class="form-label">Proveedor</label>
                                <select class="form-control" id="id_proveedor" name="id_proveedor">
                                    <option value="">Seleccionar Proveedor</option>
                                    <?php foreach ($proveedores as $proveedor): ?>
                                        <option value="<?php echo $proveedor['id_proveedor']; ?>"
                                            <?php echo $proveedor['id_proveedor'] == $producto['id_proveedor'] ? 'selected' : ''; ?>>
                                            <?php echo $proveedor['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo_sku" class="form-label">Código SKU</label>
                                <input type="text" class="form-control" id="codigo_sku" name="codigo_sku"
                                    value="<?php echo $producto['codigo_sku']; ?>" readonly>
                                <div class="form-text">El código SKU no se puede modificar</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="imagen_url" class="form-label">URL de Imagen</label>
                                <input type="url" class="form-control" id="imagen_url" name="imagen_url"
                                    value="<?php echo $producto['imagen_url']; ?>" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="pulgadas" class="form-label">Pulgadas</label>
                                <input type="number" class="form-control" id="pulgadas" name="pulgadas"
                                    value="<?php echo $producto['pulgadas']; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="resolucion" class="form-label">Resolución</label>
                                <select class="form-control" id="resolucion" name="resolucion" required>
                                    <option value="">Seleccionar Resolución</option>
                                    <option value="HD" <?php echo $producto['resolucion'] == 'HD' ? 'selected' : ''; ?>>HD</option>
                                    <option value="FULL HD" <?php echo $producto['resolucion'] == 'FULL HD' ? 'selected' : ''; ?>>FULL HD</option>
                                    <option value="4K" <?php echo $producto['resolucion'] == '4K' ? 'selected' : ''; ?>>4K</option>
                                    <option value="8K" <?php echo $producto['resolucion'] == '8K' ? 'selected' : ''; ?>>8K</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="garantia_meses" class="form-label">Garantía (meses)</label>
                                <input type="number" class="form-control" id="garantia_meses" name="garantia_meses"
                                    value="<?php echo $producto['garantia_meses']; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="hdmi_puertos" class="form-label">Puertos HDMI</label>
                                <input type="number" class="form-control" id="hdmi_puertos" name="hdmi_puertos"
                                    value="<?php echo $producto['hdmi_puertos']; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="usb_puertos" class="form-label">Puertos USB</label>
                                <input type="number" class="form-control" id="usb_puertos" name="usb_puertos"
                                    value="<?php echo $producto['usb_puertos']; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="smart_tv" name="smart_tv" value="1"
                                    <?php echo $producto['smart_tv'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="smart_tv">Smart TV</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>