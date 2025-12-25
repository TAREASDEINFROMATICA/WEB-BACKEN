<?php
$title = "Editar Categoría - " . $categoria['nombre_categoria'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-edit"></i> Editar Categoría
                </h4>
            </div>
            <div class="card-body">
                <form action="<?php echo HTTP_BASE; ?>/?c=CategoriasController&a=editar&id=<?php echo $categoria['id_categoria']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="nombre_categoria" class="form-label">Nombre de la Categoría *</label>
                        <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria"
                            value="<?php echo $categoria['nombre_categoria']; ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo HTTP_BASE; ?>/?c=CategoriasController&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>