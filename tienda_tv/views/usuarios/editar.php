<?php
$title = "Editar Usuario - " . $usuario['nombre'];
include __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1><i class="fas fa-user-edit"></i> Editar Usuario</h1>
        <p class="lead">Modificar información del usuario</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Usuario</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo HTTP_BASE; ?>/?c=Usuarios&a=editar&id=<?php echo $usuario['id_usuario']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ci" class="form-label">Cédula de Identidad *</label>
                                <input type="text" class="form-control" id="ci" name="ci"
                                    value="<?php echo htmlspecialchars($usuario['ci']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                    value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico *</label>
                                <input type="email" class="form-control" id="correo" name="correo"
                                    value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2"><?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de Usuario *</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars($usuario['username']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Dejar en blanco para mantener la actual">
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol *</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="CLIENTE" <?php echo $usuario['rol'] == 'CLIENTE' ? 'selected' : ''; ?>>Cliente</option>
                                    <option value="ADMINISTRADOR" <?php echo $usuario['rol'] == 'ADMINISTRADOR' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="genero" class="form-label">Género</label>
                                <select class="form-select" id="genero" name="genero">
                                    <option value="">Seleccionar...</option>
                                    <option value="M" <?php echo ($usuario['genero'] ?? '') == 'M' ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="F" <?php echo ($usuario['genero'] ?? '') == 'F' ? 'selected' : ''; ?>>Femenino</option>
                                    <option value="O" <?php echo ($usuario['genero'] ?? '') == 'O' ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                            value="<?php echo $usuario['fecha_nacimiento'] ?? ''; ?>">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo HTTP_BASE; ?>/?c=Usuarios&a=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>