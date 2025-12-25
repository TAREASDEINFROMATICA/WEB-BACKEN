<?php
$title = "Crear Marca";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-plus"></i> Crear Nueva Marca
                </h4>
            </div>
            <div class="card-body">
                <form action="<?php echo HTTP_BASE; ?>/?c=MarcasController&a=crear" method="POST">
                    <div class="mb-3">
                        <label for="nombre_marca" class="form-label">Nombre de la Marca *</label>
                        <input type="text" class="form-control" id="nombre_marca" name="nombre_marca" required>
                        <div class="form-text">Nombre de la marca de televisores (ej: Samsung, LG, Sony)</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo HTTP_BASE; ?>/?c=MarcasController&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Crear Marca
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
                <h5 class="card-title">Información Importante</h5>
                <div class="alert alert-info">
                    <small>
                        <strong><i class="fas fa-info-circle"></i> Campos obligatorios:</strong><br>
                        • Nombre de la marca<br><br>
                        
                        <strong><i class="fas fa-lightbulb"></i> Recomendaciones:</strong><br>
                        • Usa nombres descriptivos<br>
                        • Evita duplicados<br>
                        • Las marcas se usan para filtrar productos
                    </small>
                </div>
            </div>
        </div>
    </div>
-->
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>