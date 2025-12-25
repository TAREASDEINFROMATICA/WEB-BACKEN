<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/ProductoModel.php';
require_once ROOT_CORE . '/../models/InventarioModel.php';
require_once ROOT_CORE . '/../models/MarcaModel.php';
require_once ROOT_CORE . '/../models/CategoriaModel.php';
require_once ROOT_CORE . '/../models/ProveedorModel.php';

class ProductosController
{
    private $productoModel;
    private $inventarioModel;
    private $marcaModel;
    private $categoriaModel;
    private $proveedorModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->inventarioModel = new InventarioModel();
        $this->marcaModel = new MarcaModel();
        $this->categoriaModel = new CategoriaModel();
        $this->proveedorModel = new ProveedorModel();
    }

    public function index()
    {
        $productos = $this->productoModel->getAll();

        foreach ($productos as &$producto) {
             $inventario= $this->inventarioModel->getByProductId($producto['id_producto']);
            $producto['stock_actual'] = $inventario ? $inventario['stock_actual'] : 0;
            $producto['disponible'] = $inventario && $inventario['stock_actual'] > 0;
        }

        if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] == 'ADMINISTRADOR') {
            include ROOT_VIEW . '/admin/productos/index.php';
        } else {
            include ROOT_VIEW . '/client/productos/index.php';
        }
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
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'id_marca' => $_POST['id_marca'],
                'id_categoria' => $_POST['id_categoria'],
                'id_proveedor' => $_POST['id_proveedor'],
                'codigo_sku' => 'TV-' . $_POST['codigo_sku'],
                'imagen_url' => $_POST['imagen_url'],
                'pulgadas' => $_POST['pulgadas'],
                'resolucion' => $_POST['resolucion'],
                'smart_tv' => isset($_POST['smart_tv']) ? 1 : 0,
                'hdmi_puertos' => $_POST['hdmi_puertos'],
                'usb_puertos' => $_POST['usb_puertos'],
                'garantia_meses' => $_POST['garantia_meses']
            ];

            if ($this->productoModel->create($data)) {
                $lastProductId = $this->productoModel->getLastInsertId();

                $inventarioData = [
                    'id_producto' => $lastProductId,
                    'stock_actual' => 0,
                    'stock_minimo' => 5,
                    'stock_maximo' => 100,
                    'actualizado_en' => date('Y-m-d H:i:s')
                ];

                $inventarioResult = $this->inventarioModel->create($inventarioData);

                if ($inventarioResult) {
                    $_SESSION['success'] = 'Producto creado correctamente y agregado al inventario';
                } else {
                    $_SESSION['success'] = 'Producto creado, pero hubo un error al agregarlo al inventario';
                }

                header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
                exit;
            } else {
                $_SESSION['error'] = 'Error al crear producto';
                $marcas = $this->marcaModel->getAll();
                $categorias = $this->categoriaModel->getAll();
                $proveedores = $this->proveedorModel->getAll();
                include ROOT_VIEW . '/admin/productos/crear.php';
            }
        } else {
            $marcas = $this->marcaModel->getAll();
            $categorias = $this->categoriaModel->getAll();
            $proveedores = $this->proveedorModel->getAll();
            include ROOT_VIEW . '/admin/productos/crear.php';
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
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'id_marca' => $_POST['id_marca'],
                'id_categoria' => $_POST['id_categoria'],
                'id_proveedor' => $_POST['id_proveedor'],
                'imagen_url' => $_POST['imagen_url'],
                'pulgadas' => $_POST['pulgadas'],
                'resolucion' => $_POST['resolucion'],
                'smart_tv' => isset($_POST['smart_tv']) ? 1 : 0,
                'hdmi_puertos' => $_POST['hdmi_puertos'],
                'usb_puertos' => $_POST['usb_puertos'],
                'garantia_meses' => $_POST['garantia_meses']
            ];

            if ($this->productoModel->update($id, $data)) {
                $_SESSION['success'] = 'Producto actualizado correctamente';
                header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
            } else {
                $_SESSION['error'] = 'Error al actualizar producto';
                $producto = $this->productoModel->getById($id);
                $marcas = $this->marcaModel->getAll();
                $categorias = $this->categoriaModel->getAll();
                $proveedores = $this->proveedorModel->getAll();
                include ROOT_VIEW . '/admin/productos/editar.php';
            }
        } else {
            $producto = $this->productoModel->getById($id);
            if (!$producto) {
                $_SESSION['error'] = 'Producto no encontrado';
                header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
                exit;
            }
            $marcas = $this->marcaModel->getAll();
            $categorias = $this->categoriaModel->getAll();
            $proveedores = $this->proveedorModel->getAll();
            include ROOT_VIEW . '/admin/productos/editar.php';
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

        if ($this->productoModel->delete($id)) {
            $_SESSION['success'] = 'Producto eliminado correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar producto';
        }

        header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
    }

    public function detalle($id = null)
    {
        $id = $id ?? $_GET['id'];
        $producto = $this->productoModel->getById($id);

        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado';
            header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
            exit;
        }

        $inventario = $this->inventarioModel->getByProductId($id);
        $producto['stock_actual'] = $inventario ? $inventario['stock_actual'] : 0;
        $producto['disponible'] = $inventario && $inventario['stock_actual'] > 0;

        include ROOT_VIEW . '/client/productos/detalle.php';
    }
}
