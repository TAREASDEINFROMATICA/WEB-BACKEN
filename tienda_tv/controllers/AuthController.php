<?php
class AuthController
{
    private $usuarioModel;
    private $loginCookie;

    public function __construct()
    {
        if (file_exists(ROOT_CORE . '/Model.php')) {
            require_once ROOT_CORE . '/Model.php';
        }
        if (file_exists(ROOT_CORE . '/../models/UsuarioModel.php')) {
            require_once ROOT_CORE . '/../models/UsuarioModel.php';
        }
        require_once ROOT_CORE . '/LoginCookie.php';
        $this->usuarioModel = new UsuarioModel();
        $this->loginCookie  = new LoginCookie();
    }

    public function index()
    {
        include 'views/home/index.php';
    }

    public function catalogo()
    {
        if (isset($_SESSION['usuario'])) {
            if ($_SESSION['usuario']['rol'] === 'ADMINISTRADOR') {
                header('Location: ' . HTTP_BASE . '/?c=AdminController&a=dashboard');
            } else {
                header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
            }
        } else {
            header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
        }
        exit;
    }

    public function login()
    {
        
        // Puede redirigir si ya hay sesión por cookie
        $this->verificarAutenticacionAuto();

        if (isset($_SESSION['usuario'])) {
            if ($_SESSION['usuario']['rol'] === 'CLIENTE') {
                header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
            } elseif ($_SESSION['usuario']['rol'] === 'ADMINISTRADOR') {
                header('Location: ' . HTTP_BASE . '/?c=AdminController&a=dashboard');
            }
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $recordar = isset($_POST['recordar']);

            if (isset($this->usuarioModel)) {
                $usuario = $this->usuarioModel->getByEmail($username);

                if ($usuario && password_verify($password, $usuario['password_hash'])) {
                    $_SESSION['usuario'] = $usuario;
                    $_SESSION['success'] = 'Bienvenido ' . $usuario['nombre'];

                    // Cookie 
                    if ($recordar) {
                        $this->loginCookie->crearCookieLogin(
                            $usuario['id_usuario'],
                            $usuario['username'],
                            true
                        );
                    }

                    // Si es cliente, marcar sync de carrito
                    if ($usuario['rol'] === 'CLIENTE') {
                        $this->sincronizarCarritoDespuesLogin($usuario['id_usuario']);
                        header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
                    } else {
                        header('Location: ' . HTTP_BASE . '/?c=AdminController&a=dashboard');
                    }
                    exit;
                } else {
                    $_SESSION['error'] = 'Credenciales incorrectas';
                }
            } else {
                $_SESSION['error'] = 'Sistema de autenticación no disponible';
            }
        }

        include ROOT_VIEW . '/auth/login.php';
    }

    private function sincronizarCarritoDespuesLogin($userId)
    {

        $_SESSION['carrito_pendiente_sincronizar'] = true;

    }

  public function register()
{
    if (isset($_SESSION['usuario'])) {
        header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($this->usuarioModel)) {

            $rolSeleccionado = $_POST['rol'] ?? 'CLIENTE'; 

            $data = [
                'nombre'           => $_POST['nombre'] ?? '',
                'ci'               => $_POST['ci'] ?? '',
                'telefono'         => $_POST['telefono'] ?? '',
                'direccion'        => $_POST['direccion'] ?? '',
                'correo'           => $_POST['correo'] ?? '',
                'username'         => $_POST['username'] ?? '',
                'password_hash'    => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
                'rol'              => $rolSeleccionado,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'genero'           => $_POST['genero'] ?? null
            ];

            try {
                if ($this->usuarioModel->create($data)) {
                    $_SESSION['success'] = 'Usuario registrado correctamente como ' . $rolSeleccionado;
                    header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
                    exit;
                } else {
                    $_SESSION['error'] = 'Error al registrar. Intente con datos diferentes.';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Sistema de registro no disponible';
        }
    }

    include ROOT_VIEW . '/auth/register.php';
}



    public function logout()
    {
        // Borrar cookie de login persistente se cierra se borra el guardado de la cookie 
        $this->loginCookie->eliminarCookieLogin();

        session_destroy();
        header('Location: ' . HTTP_BASE . '/?c=Auth&a=catalogo');
        exit;
    }

    // Autologin por cookie "recordarme"
    private function verificarAutenticacionAuto()
    {
        if (isset($_SESSION['usuario'])) {
            return true;
        }

        $usuarioCookie = $this->loginCookie->obtenerUsuarioDeCookie();
        if ($usuarioCookie) {
            $usuario = $this->usuarioModel->getById($usuarioCookie['id']);

            if ($usuario && (int)$usuario['estado'] === 1) {
                $_SESSION['usuario'] = $usuario;
                $_SESSION['success'] = 'Bienvenido de nuevo ' . $usuario['nombre'];

                if ($usuario['rol'] === 'CLIENTE') {
                    header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
                } else {
                    header('Location: ' . HTTP_BASE . '/?c=AdminController&a=dashboard');
                }
                exit;
            } else {
                
                $this->loginCookie->eliminarCookieLogin();
            }
        }

        return false;
    }
}
