<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/MarcaModel.php';

class MarcasController
{
    private $marcaModel;

    public function __construct()
    {
        $this->marcaModel = new MarcaModel();
    }

    public function index()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $marcas = $this->marcaModel->getAll();
        include ROOT_VIEW . '/admin/marcas/index.php';
    }

    public function crear()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre_marca'];

            if ($this->marcaModel->create($nombre)) {
                $_SESSION['success'] = 'Marca creada correctamente';
                header('Location: ' . HTTP_BASE . '/?c=MarcasController&a=index');
            } else {
                $_SESSION['error'] = 'Error al crear marca';
                include ROOT_VIEW . '/admin/marcas/crear.php';
            }
        } else {
            include ROOT_VIEW . '/admin/marcas/crear.php';
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
            $nombre = $_POST['nombre_marca'];

            if ($this->marcaModel->update($id, $nombre)) {
                $_SESSION['success'] = 'Marca actualizada correctamente';
                header('Location: ' . HTTP_BASE . '/?c=MarcasController&a=index');
            } else {
                $_SESSION['error'] = 'Error al actualizar marca';
                $marca = $this->marcaModel->getById($id);
                include ROOT_VIEW . '/admin/marcas/editar.php';
            }
        } else {
            $marca = $this->marcaModel->getById($id);
            if (!$marca) {
                $_SESSION['error'] = 'Marca no encontrada';
                header('Location: ' . HTTP_BASE . '/?c=MarcasController&a=index');
                exit;
            }
            include ROOT_VIEW . '/admin/marcas/editar.php';
        }
    }

    public function eliminar($id = null)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $id = $id ?? $_GET['id'];

        if ($this->marcaModel->delete($id)) {
            $_SESSION['success'] = 'Marca eliminada correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar marca';
        }

        header('Location: ' . HTTP_BASE . '/?c=MarcasController&a=index');
    }
}
