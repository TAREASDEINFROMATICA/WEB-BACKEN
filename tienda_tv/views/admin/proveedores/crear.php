<?php
$title = "Crear Proveedor";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-plus"></i> Crear Nuevo Proveedor
                </h4>
            </div>
            <div class="card-body">
                <form action="<?php echo HTTP_BASE; ?>/?c=ProveedoresController&a=crear" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Proveedor *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                <div class="form-text">Nombre completo de la empresa proveedora</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="3"
                            placeholder="Dirección completa del proveedor..."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo HTTP_BASE; ?>/?c=ProveedoresController&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Crear Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!---
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Información Importante</h5>
                <div class="alert alert-info">
                    <small>
                        <strong><i class="fas fa-info-circle"></i> Campos obligatorios:</strong><br>
                        • Nombre del proveedor<br><br>
                        
                        <strong><i class="fas fa-lightbulb"></i> Recomendaciones:</strong><br>
                        • Completa todos los campos para mejor control<br>
                        • Verifica la información de contacto<br>
                        • Mantén actualizados los datos
                    </small>
                </div>
            </div>
        </div>
    </div>  
    -->
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>