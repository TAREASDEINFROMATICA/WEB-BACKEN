<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/ProductoModel.php';
require_once ROOT_CORE . '/../models/UsuarioModel.php';
require_once ROOT_CORE . '/../models/VentaModel.php';
require_once ROOT_CORE . '/../models/InventarioModel.php';
require_once ROOT_CORE . '/../models/ProveedorModel.php';

class AdminController
{
    private $productoModel;
    private $usuarioModel;
    private $ventaModel;
    private $inventarioModel;

    public function __construct()
    {
        $this->productoModel   = new ProductoModel();
        $this->usuarioModel    = new UsuarioModel();
        $this->ventaModel      = new VentaModel();
        $this->inventarioModel = new InventarioModel();
    }

    public function dashboard()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'ADMINISTRADOR') {
            $_SESSION['error'] = 'No tienes permisos de administrador';
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=index');
            exit;
        }

        $proveedorModel = new ProveedorModel();
        //para evitar que se dupliquen los datos
        $productos = $this->productoModel->getAll();
        $usuarios  = $this->usuarioModel->getAll();
        $ventas    = $this->ventaModel->getAll();

        $stats = [
            'total_proveedores' => count($proveedorModel->getAll()),
            'total_productos'   => count($productos),
            'total_usuarios'    => count($usuarios),
            'total_ventas'      => count($ventas),
            'stock_bajo'        => $this->inventarioModel->getLowStock(),
            'ultimas_ventas'    => array_slice($ventas, 0, 20), 
        ];

        include ROOT_VIEW . '/admin/dashboard.php';
    }
}
