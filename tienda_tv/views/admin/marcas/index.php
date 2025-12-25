<?php
$title = "Gestión de Marcas";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-tags"></i> Gestión de Marcas</h1>
            <a href="<?php echo HTTP_BASE; ?>/?c=MarcasController&a=crear" class="btn btn-success">
                <i class="fas fa-plus"></i> Nueva Marca
            </a>
        </div>
        <p class="lead">Administra las marcas de televisores</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Lista de Marcas
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($marcas)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay marcas registradas.
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
                                <?php foreach ($marcas as $marca): ?>
                                    <tr>
                                        <td><?php echo $marca['id_marca']; ?></td>
                                        <td>
                                            <strong><?php echo $marca['nombre_marca']; ?></strong>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo HTTP_BASE; ?>/?c=MarcasController&a=editar&id=<?php echo $marca['id_marca']; ?>"
                                                    class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <a href="<?php echo HTTP_BASE; ?>/?c=MarcasController&a=eliminar&id=<?php echo $marca['id_marca']; ?>"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta marca?')">
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