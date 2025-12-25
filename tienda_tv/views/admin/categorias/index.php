<?php
$title = "Gestión de Categorías";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-layer-group"></i> Gestión de Categorías</h1>
            <a href="<?php echo HTTP_BASE; ?>/?c=CategoriasController&a=crear" class="btn btn-success">
                <i class="fas fa-plus"></i> Nueva Categoría
            </a>
        </div>
        <p class="lead">Administra las categorías de productos</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Lista de Categorías
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($categorias)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay categorías registradas.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td><?php echo $categoria['id_categoria']; ?></td>
                                        <td>
                                            <strong><?php echo $categoria['nombre_categoria']; ?></strong>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo HTTP_BASE; ?>/?c=CategoriasController&a=editar&id=<?php echo $categoria['id_categoria']; ?>"
                                                    class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <a href="<?php echo HTTP_BASE; ?>/?c=CategoriasController&a=eliminar&id=<?php echo $categoria['id_categoria']; ?>"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>