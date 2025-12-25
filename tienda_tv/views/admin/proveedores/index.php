<?php
$title = "Gestión de Proveedores";
include __DIR__ . '/../../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-truck"></i> Gestión de Proveedores</h1>
            <a href="<?php echo HTTP_BASE; ?>/?c=ProveedoresController&a=crear" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Proveedor
            </a>
        </div>
        <p class="lead">Administra los proveedores de la tienda</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Lista de Proveedores
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($proveedores)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay proveedores registrados.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Dirección</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <tr>
                                        <td><?php echo $proveedor['id_proveedor']; ?></td>
                                        <td>
                                            <strong><?php echo $proveedor['nombre']; ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($proveedor['telefono']): ?>
                                                <i class="fas fa-phone"></i> <?php echo $proveedor['telefono']; ?>
                                            <?php else: ?>
                                                <span class="text-muted">No especificado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($proveedor['correo']): ?>
                                                <i class="fas fa-envelope"></i> <?php echo $proveedor['correo']; ?>
                                            <?php else: ?>
                                                <span class="text-muted">No especificado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($proveedor['direccion']): ?>
                                                <i class="fas fa-map-marker-alt"></i> <?php echo $proveedor['direccion']; ?>
                                            <?php else: ?>
                                                <span class="text-muted">No especificada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo HTTP_BASE; ?>/?c=ProveedoresController&a=editar&id=<?php echo $proveedor['id_proveedor']; ?>"
                                                    class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <a href="<?php echo HTTP_BASE; ?>/?c=ProveedoresController&a=eliminar&id=<?php echo $proveedor['id_proveedor']; ?>"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('¿Estás seguro de eliminar este proveedor?')">
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