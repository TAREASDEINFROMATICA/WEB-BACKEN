<?php
$title = "Iniciar Sesión";
include __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </h4>
            </div>
            <div class="card-body">
                <form action="<?php echo HTTP_BASE; ?>/?c=Auth&a=login" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Correo Electrónico *</label>
                        <input type="email" class="form-control" id="username" name="username" required
                            placeholder="usuario@ejemplo.com">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña *</label>
                        <input type="password" class="form-control" id="password" name="password" required
                            placeholder="Ingresa tu contraseña">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="recordar" value="1">
                            Recordar mi sesión
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </button>
                    </div>


                </form>

                <div class="text-center mt-3">
                    <p>¿No tienes cuenta?
                        <a href="<?php echo HTTP_BASE; ?>/?c=Auth&a=register" class="text-decoration-none">
                            <strong>Regístrate aquí</strong>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>