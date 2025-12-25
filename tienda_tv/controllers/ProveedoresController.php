<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/ProveedorModel.php';

class ProveedoresController
{
    private $proveedorModel;

    public function __construct()
    {
        $this->proveedorModel = new ProveedorModel();
    }

    public function index()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos de administrador';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        $proveedores = $this->proveedorModel->getAll();
        include ROOT_VIEW . '/admin/proveedores/index.php';
    }

    public function crear()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos de administrador';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre']),
                'telefono' => trim($_POST['telefono']),
                'direccion' => trim($_POST['direccion']),
                'correo' => trim($_POST['correo'])
            ];

            if ($this->proveedorModel->create($data)) {
                $_SESSION['success'] = 'Proveedor creado correctamente';
                header('Location: ' . HTTP_BASE . '/?c=ProveedoresController&a=index');
                exit;
            } else {
                $_SESSION['error'] = 'Error al crear proveedor';
                include ROOT_VIEW . '/admin/proveedores/crear.php';
            }
        } else {
            include ROOT_VIEW . '/admin/proveedores/crear.php';
        }
    }

    public function editar($id = null)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos de administrador';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        $id = $id ?? $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre']),
                'telefono' => trim($_POST['telefono']),
                'direccion' => trim($_POST['direccion']),
                'correo' => trim($_POST['correo'])
            ];

            if ($this->proveedorModel->update($id, $data)) {
                $_SESSION['success'] = 'Proveedor actualizado correctamente';
                header('Location: ' . HTTP_BASE . '/?c=ProveedoresController&a=index');
                exit;
            } else {
                $_SESSION['error'] = 'Error al actualizar proveedor';
                $proveedor = $this->proveedorModel->getById($id);
                include ROOT_VIEW . '/admin/proveedores/editar.php';
            }
        } else {
            $proveedor = $this->proveedorModel->getById($id);
            if (!$proveedor) {
                $_SESSION['error'] = 'Proveedor no encontrado';
                header('Location: ' . HTTP_BASE . '/?c=ProveedoresController&a=index');
                exit;
            }
            include ROOT_VIEW . '/admin/proveedores/editar.php';
        }
    }

    public function eliminar($id = null)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos de administrador';
            header('Location: ' . HTTP_BASE . '/?c=Home&a=index');
            exit;
        }

        $id = $id ?? $_GET['id'];

        if ($this->proveedorModel->delete($id)) {
            $_SESSION['success'] = 'Proveedor eliminado correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar proveedor';
        }

        header('Location: ' . HTTP_BASE . '/?c=ProveedoresController&a=index');
    }
}
