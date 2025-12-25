<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/UsuarioModel.php';

class UsuariosController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        $usuarios = $this->usuarioModel->getAll();
        include ROOT_VIEW . '/usuarios/index.php';
    }

    public function crear()
    {
        
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $data = [
                'nombre' => trim($_POST['nombre']),
                'ci' => trim($_POST['ci']),
                'telefono' => isset($_POST['telefono']) ? trim($_POST['telefono']) : '',
                'direccion' => isset($_POST['direccion']) ? trim($_POST['direccion']) : '',
                'correo' => trim($_POST['correo']),
                'username' => trim($_POST['username']),
                'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'rol' => $_POST['rol'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'genero' => $_POST['genero'] ?? null
            ];

            if ($this->usuarioModel->create($data)) {
                $_SESSION['success'] = 'Usuario creado correctamente';
                header('Location: ' . HTTP_BASE . '/?c=Usuarios&a=index');
            } else {
                $_SESSION['error'] = 'Error al crear usuario';
                include ROOT_VIEW . '/usuarios/crear.php';
            }
        } else {
            include ROOT_VIEW . '/usuarios/crear.php';
        }
    }
    public function editar($id = null)
    {
        
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        $id = $id ?? $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre']),
                'ci' => trim($_POST['ci']),
                'telefono' => trim($_POST['telefono']),
                'direccion' => trim($_POST['direccion']),
                'correo' => trim($_POST['correo']),
                'username' => trim($_POST['username']),
                'rol' => $_POST['rol'],
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'genero' => $_POST['genero']
            ];

            
            if (!empty($_POST['password'])) {
                $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            if ($this->usuarioModel->update($id, $data)) {
                $_SESSION['success'] = 'Usuario actualizado correctamente';
                header('Location: ' . HTTP_BASE . '/?c=Usuarios&a=index');
            } else {
                $_SESSION['error'] = 'Error al actualizar usuario';
                $usuario = $this->usuarioModel->getById($id);
                include ROOT_VIEW . '/usuarios/editar.php';
            }
        } else {
            $usuario = $this->usuarioModel->getById($id);
            if (!$usuario) {
                $_SESSION['error'] = 'Usuario no encontrado';
                header('Location: ' . HTTP_BASE . '/?c=Usuarios&a=index');
                exit;
            }
            include ROOT_VIEW . '/usuarios/editar.php';
        }
    }
    public function eliminar($id = null)
    {
        
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos para esta acción';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        $id = $id ?? $_GET['id'];

        
        if ($id == $_SESSION['usuario']['id_usuario']) {
            $_SESSION['error'] = 'No puedes eliminar tu propio usuario';
            header('Location: ' . HTTP_BASE . '/?c=Usuarios&a=index');
            exit;
        }

        if ($this->usuarioModel->delete($id)) {
            $_SESSION['success'] = 'Usuario eliminado permanentemente';
        } else {
            $_SESSION['error'] = 'Error al eliminar usuario';
        }

        header('Location: ' . HTTP_BASE . '/?c=Usuarios&a=index');
    }
    public function perfil($id = null)
    {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['error'] = 'Debes iniciar sesión';
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $id_usuario = $id ?? $_SESSION['usuario']['id_usuario'];

        if ($_SESSION['usuario']['rol'] == 'CLIENTE' && $id_usuario != $_SESSION['usuario']['id_usuario']) {
            $_SESSION['error'] = 'Solo puedes editar tu propio perfil';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Preparar datos asegurando que no haya campos vacíos
            $data = [
                'nombre' => trim($_POST['nombre']),
                'ci' => trim($_POST['ci']),
                'correo' => trim($_POST['correo']),
                'username' => trim($_POST['username']),
                'rol' => $_SESSION['usuario']['rol'] // Siempre mantener el rol actual
            ];

            // Campos opcionales - solo si tienen valor
            if (!empty(trim($_POST['telefono']))) {
                $data['telefono'] = trim($_POST['telefono']);
            }
            if (!empty(trim($_POST['direccion']))) {
                $data['direccion'] = trim($_POST['direccion']);
            }
            if (!empty($_POST['fecha_nacimiento'])) {
                $data['fecha_nacimiento'] = $_POST['fecha_nacimiento'];
            }
            if (!empty($_POST['genero'])) {
                $data['genero'] = $_POST['genero'];
            }

            
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    $_SESSION['error'] = 'La contraseña esta mal';
                    $usuario = $this->usuarioModel->getById($id_usuario);
                    include ROOT_VIEW . '/usuarios/perfil.php';
                    return;
                }
                $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            
            if ($_SESSION['usuario']['rol'] == 'ADMINISTRADOR' && isset($_POST['rol'])) {
                $data['rol'] = $_POST['rol'];
            }

            // Debug
            error_log("Datos a actualizar: " . print_r($data, true));

            if ($this->usuarioModel->update($id_usuario, $data)) {
                // Actualizar datos en sesión
                $_SESSION['usuario']['nombre'] = $data['nombre'];
                $_SESSION['usuario']['correo'] = $data['correo'];
                $_SESSION['usuario']['username'] = $data['username'];

                $_SESSION['success'] = 'Perfil actualizado correctamente';
                header('Location: ' . HTTP_BASE . '/?c=UsuariosController&a=perfil&id=' . $id_usuario);
                exit;
            } else {
                $_SESSION['error'] = 'Error al actualizar perfil. Verifica los datos.';
                // Debug adicional
                error_log("FALLÓ la actualización del usuario ID: " . $id_usuario);
                $usuario = $this->usuarioModel->getById($id_usuario);
                include ROOT_VIEW . '/usuarios/perfil.php';
            }
        } else {
            $usuario = $this->usuarioModel->getById($id_usuario);
            if (!$usuario) {
                $_SESSION['error'] = 'Usuario no encontrado';
                header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
                exit;
            }
            include ROOT_VIEW . '/usuarios/perfil.php';
        }
    }
    public function actualizarPerfil()
    {
        
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['success' => false, 'message' => 'No autenticado']);
            exit;
        }

        $id_usuario = $_SESSION['usuario']['id_usuario'];
        $data = [
            'nombre' => trim($_POST['nombre']),
            'telefono' => trim($_POST['telefono']),
            'direccion' => trim($_POST['direccion'])
        ];

        if ($this->usuarioModel->update($id_usuario, $data)) {
            $_SESSION['usuario']['nombre'] = $data['nombre'];
            echo json_encode(['success' => true, 'message' => 'Perfil actualizado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
    }
    public function ver($id = null)
    {
        
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        $id = $id ?? $_GET['id'];
        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: ' . HTTP_BASE . '/?c=Usuarios&a=index');
            exit;
        }

        include ROOT_VIEW . '/usuarios/ver.php';
    }
}
