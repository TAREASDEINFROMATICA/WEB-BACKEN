<?php
$title = "Gestión de Usuarios";
include __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
            <a href="<?php echo HTTP_BASE; ?>/?c=UsuariosController&a=crear" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>
        </div>
        <p class="lead">Administra los usuarios del sistema</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Lista de Usuarios
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($usuarios)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay usuarios registrados.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Rol</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo $usuario['id_usuario']; ?></td>
                                        <td>
                                            <strong><?php echo $usuario['nombre']; ?></strong>
                                            <?php if ($usuario['id_usuario'] == $_SESSION['usuario']['id_usuario']): ?>
                                                <span class="badge bg-primary">Tú</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $usuario['correo']; ?></td>
                                        <td><?php echo $usuario['username']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $usuario['rol'] == 'ADMINISTRADOR' ? 'danger' : 'success'; ?>">
                                                <?php echo $usuario['rol']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $usuario['telefono'] ?: 'N/A'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $usuario['estado'] ? 'success' : 'danger'; ?>">
                                                <?php echo $usuario['estado'] ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo HTTP_BASE; ?>/?c=UsuariosController&a=ver&id=<?php echo $usuario['id_usuario']; ?>"
                                                    class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                                <a href="<?php echo HTTP_BASE; ?>/?c=UsuariosController&a=editar&id=<?php echo $usuario['id_usuario']; ?>" 
                                                class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                              
                                                <?php if ($usuario['id_usuario'] != $_SESSION['usuario']['id_usuario']): ?>
                                                    <a href="<?php echo HTTP_BASE; ?>/?c=UsuariosController&a=eliminar&id=<?php echo $usuario['id_usuario']; ?>"
                                                        class="btn btn-outline-danger"
                                                        onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </a>
                                                <?php endif; ?>

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

<?php include __DIR__ . '/../layouts/footer.php'; ?>