<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/CategoriaModel.php';

class CategoriasController
{
    private $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    public function index()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $categorias = $this->categoriaModel->getAll();
        include ROOT_VIEW . '/admin/categorias/index.php';
    }

    public function crear()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre_categoria'];

            if ($this->categoriaModel->create($nombre)) {
                $_SESSION['success'] = 'Categoría creada correctamente';
                header('Location: ' . HTTP_BASE . '/?c=Categorias&a=index');
            } else {
                $_SESSION['error'] = 'Error al crear categoría';
                include ROOT_VIEW . '/admin/categorias/crear.php';
            }
        } else {
            include ROOT_VIEW . '/admin/categorias/crear.php';
        }
    }

    public function editar($id = null)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $id = $id ?? $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre_categoria'];

            if ($this->categoriaModel->update($id, $nombre)) {
                $_SESSION['success'] = 'Categoría actualizada correctamente';
                header('Location: ' . HTTP_BASE . '/?c=Categorias&a=index');
            } else {
                $_SESSION['error'] = 'Error al actualizar categoría';
                $categoria = $this->categoriaModel->getById($id);
                include ROOT_VIEW . '/admin/categorias/editar.php';
            }
        } else {
            $categoria = $this->categoriaModel->getById($id);
            if (!$categoria) {
                $_SESSION['error'] = 'Categoría no encontrada';
                header('Location: ' . HTTP_BASE . '/?c=Categorias&a=index');
                exit;
            }
            include ROOT_VIEW . '/admin/categorias/editar.php';
        }
    }

    public function eliminar($id = null)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $id = $id ?? $_GET['id'];

        if ($this->categoriaModel->delete($id)) {
            $_SESSION['success'] = 'Categoría eliminada correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar categoría';
        }

        header('Location: ' . HTTP_BASE . '/?c=Categorias&a=index');
    }
}
