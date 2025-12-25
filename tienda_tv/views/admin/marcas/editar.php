<?php
$title = "Editar Marca - " . $marca['nombre_marca'];
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-edit"></i> Editar Marca
                </h4>
            </div>
            <div class="card-body">
                <form action="<?php echo HTTP_BASE; ?>/?c=MarcasController&a=editar&id=<?php echo $marca['id_marca']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="nombre_marca" class="form-label">Nombre de la Marca *</label>
                        <input type="text" class="form-control" id="nombre_marca" name="nombre_marca"
                            value="<?php echo $marca['nombre_marca']; ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?php echo HTTP_BASE; ?>/?c=MarcasController&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Marca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>