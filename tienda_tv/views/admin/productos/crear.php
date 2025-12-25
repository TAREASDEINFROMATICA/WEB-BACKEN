<?php
$title = "Crear Producto - Admin";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-plus"></i> Crear Nuevo Producto
                </h4>
            </div>
            <div class="card-body">
                <form action="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=crear" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Producto *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio *</label>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="id_marca" class="form-label">Marca</label>
                                <select class="form-control" id="id_marca" name="id_marca">
                                    <option value="">Seleccionar Marca</option>
                                    <?php foreach ($marcas as $marca): ?>
                                        <option value="<?php echo $marca['id_marca']; ?>"><?php echo $marca['nombre_marca']; ?></option>
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
                                        <option value="<?php echo $categoria['id_categoria']; ?>"><?php echo $categoria['nombre_categoria']; ?></option>
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
                                        <option value="<?php echo $proveedor['id_proveedor']; ?>"><?php echo $proveedor['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo_sku" class="form-label">Número de Producto *</label>
                                <input type="number" class="form-control" id="codigo_sku" name="codigo_sku"
                                    min="1" max="999999" required>
                                <div class="form-text">Ingresa solo el número (ej: 999 se convertirá en TV-999)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="imagen_url" class="form-label">Seleccionar Imagen</label>
                                    <select class="form-control" id="imagen_url" name="imagen_url">
                                        <option value="">Seleccionar una imagen</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv.jpeg">TV Modelo 1</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv1.jpeg">TV Modelo 2</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv2.jpeg">TV Modelo 3</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv3.jpeg">TV Modelo 4</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv4.jpeg">TV Modelo 5</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv5.jpeg">TV Modelo 6</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv6.jpeg">TV Modelo 7</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv7.jpeg">TV Modelo 8</option>
                                        <option value="<?php echo HTTP_BASE; ?>/public/images/productos/tv8.jpeg">TV Modelo 9</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vista Previa</label>
                                    <div id="vista-previa" class="border rounded p-2 text-center bg-light" style="height: 150px;">
                                        <small class="text-muted">Selecciona una imagen para ver la vista previa</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="pulgadas" class="form-label">Pulgadas</label>
                                    <input type="number" class="form-control" id="pulgadas" name="pulgadas">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="resolucion" class="form-label">Resolución</label>
                                    <select class="form-control" id="resolucion" name="resolucion" required>
                                        <option value="">Seleccionar Resolución</option>
                                        <option value="HD">HD</option>
                                        <option value="FULL HD">FULL HD</option>
                                        <option value="4K">4K</option>
                                        <option value="8K">8K</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="garantia_meses" class="form-label">Garantía (meses)</label>
                                    <input type="number" class="form-control" id="garantia_meses" name="garantia_meses">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="hdmi_puertos" class="form-label">Puertos HDMI</label>
                                    <input type="number" class="form-control" id="hdmi_puertos" name="hdmi_puertos" value="2">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="usb_puertos" class="form-label">Puertos USB</label>
                                    <input type="number" class="form-control" id="usb_puertos" name="usb_puertos" value="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="smart_tv" name="smart_tv" value="1">
                                    <label class="form-check-label" for="smart_tv">Smart TV</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="<?php echo HTTP_BASE; ?>/?c=ProductosController&a=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Crear Producto
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo HTTP_BASE; ?>/public/js/productos.js"></script>
<?php include __DIR__ . '/../../layouts/footer.php'; ?>