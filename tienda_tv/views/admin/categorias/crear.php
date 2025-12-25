<?php
$title = "Crear Categoría";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-plus"></i> Crear Nueva Categoría
                </h4>
            </div>
            <div class="card-body">
                <form action="<?php echo HTTP_BASE; ?>/?c=CategoriasController&a=crear" method="POST">
                    <div class="mb-3">
                        <label for="nombre_categoria" class="form-label">Nombre de la Categoría *</label>
                        <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" required>
                        <div class="form-text">Nombre de la categoría (ej: Smart TV, LED, OLED, 4K UHD)</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo HTTP_BASE; ?>/?c=CategoriasController&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Crear Categoría
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
                        • Nombre de la categoría<br><br>
                        
                        <strong><i class="fas fa-lightbulb"></i> Recomendaciones:</strong><br>
                        • Usa nombres descriptivos<br>
                        • Evita duplicados<br>
                        • Las categorías ayudan a organizar los productos
                    </small>
                </div>
            </div>
        </div>
    </div>
    -->
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>