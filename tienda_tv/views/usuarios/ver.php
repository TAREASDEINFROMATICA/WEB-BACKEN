<?php
$title = "Ver Usuario - " . $usuario['nombre'];
include __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-user"></i> Información del Usuario</h1>
            <a href="<?php echo HTTP_BASE; ?>/?c=UsuariosController&a=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Usuarios
            </a>
        </div>
        <p class="lead">Datos completos del usuario</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-circle"></i> Información Personal
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cédula de Identidad</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($usuario['ci'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <p class="form-control-plaintext"><?php echo !empty($usuario['telefono']) ? htmlspecialchars($usuario['telefono']) : 'No especificado'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($usuario['correo'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Dirección</label>
                    <p class="form-control-plaintext"><?php echo !empty($usuario['direccion']) ? htmlspecialchars($usuario['direccion']) : 'No especificada'; ?></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de Usuario</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($usuario['username'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Rol</label>
                            <p>
                                <span class="badge bg-<?php echo $usuario['rol'] == 'ADMINISTRADOR' ? 'danger' : 'success'; ?>">
                                    <?php echo $usuario['rol']; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Género</label>
                            <p class="form-control-plaintext">
                                <?php 
                                switch($usuario['genero'] ?? '') {
                                    case 'M': echo 'Masculino'; break;
                                    case 'F': echo 'Femenino'; break;
                                    case 'O': echo 'Otro'; break;
                                    default: echo 'No especificado';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de Nacimiento</label>
                            <p class="form-control-plaintext"><?php echo !empty($usuario['fecha_nacimiento']) ? date('d/m/Y', strtotime($usuario['fecha_nacimiento'])) : 'No especificada'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i> Información de la Cuenta
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Estado</label>
                    <p>
                        <span class="badge bg-<?php echo $usuario['estado'] ? 'success' : 'danger'; ?>">
                            <?php echo $usuario['estado'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Fecha de Registro</label>
                    <p class="form-control-plaintext"><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])); ?></p>
                </div>
                <?php if (!empty($usuario['ultimo_login'])): ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Último Login</label>
                    <p class="form-control-plaintext"><?php echo date('d/m/Y H:i', strtotime($usuario['ultimo_login'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>