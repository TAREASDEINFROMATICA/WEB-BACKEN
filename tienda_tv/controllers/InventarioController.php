<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/InventarioModel.php';
require_once ROOT_CORE . '/../models/ProductoModel.php';

class InventarioController
{
    private $inventarioModel;
    private $productoModel;

    public function __construct()
    {
        $this->inventarioModel = new InventarioModel();
        $this->productoModel = new ProductoModel();
    }

    public function index()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $inventario = $this->inventarioModel->getAllWithProducts();
        $stockBajo = $this->inventarioModel->getLowStock();

        include ROOT_VIEW . '/admin/inventario/index.php';
    }
    

    public function ajustar($id = null)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $id = $id ?? $_GET['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'];
            $cantidad = $_POST['cantidad'];

            if ($tipo === 'agregar') {
                $result = $this->inventarioModel->addStock($id, $cantidad);
                $mensaje = 'Stock agregado correctamente';
            } else {
                $result = $this->inventarioModel->subtractStock($id, $cantidad);
                $mensaje = 'Stock reducido correctamente';
            }

            if ($result) {
                $_SESSION['success'] = $mensaje;
            } else {
                $_SESSION['error'] = 'Error al ajustar stock';
            }

            header('Location: ' . HTTP_BASE . '/?c=InventarioController&a=index');
        } else {
            $producto = $this->productoModel->getById($id);
            $inventario = $this->inventarioModel->getByProductId($id);

            if (!$producto || !$inventario) {
                $_SESSION['error'] = 'Producto no encontrado en inventario';
                header('Location: ' . HTTP_BASE . '/?c=InventarioController&a=index');
                exit;
            }

            include ROOT_VIEW . '/admin/inventario/ajustar.php';
        }
    }
    public function eliminar($id = null)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos de administrador';
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $id = $id ?? $_GET['id'];

        try {
            $producto = $this->productoModel->getById($id);

            if (!$producto) {
                $_SESSION['error'] = 'El producto no existe';
            } else {
                if ($this->inventarioModel->delete($id)) {
                    $_SESSION['success'] = 'Registro de inventario eliminado correctamente para: ' . $producto['nombre'];
                } else {
                    $_SESSION['error'] = 'Error al eliminar el registro de inventario';
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar inventario: ' . $e->getMessage();
        }

        header('Location: ' . HTTP_BASE . '/?c=InventarioController&a=index');
        exit;
    }
}
