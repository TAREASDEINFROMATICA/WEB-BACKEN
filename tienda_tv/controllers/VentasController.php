<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/VentaModel.php';
require_once ROOT_CORE . '/../models/DetalleVentaModel.php';
require_once ROOT_CORE . '/../models/ProductoModel.php';

class VentasController
{
    private $ventaModel;
    private $detalleVentaModel;
    private $productoModel;

    public function __construct()
    {
        $this->ventaModel = new VentaModel();
        $this->detalleVentaModel = new DetalleVentaModel();
        $this->productoModel = new ProductoModel();
    }

    public function index()
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . HTTP_BASE . '/?c= Auth&a=login');
            exit;
        }

        
        if ($_SESSION['usuario']['rol'] == 'CLIENTE') {
            $ventas = $this->ventaModel->getSalesByUser($_SESSION['usuario']['id_usuario']);
        } else {
            $ventas = $this->ventaModel->getAll();
        }

        include ROOT_VIEW . '/client/ventas/index.php';
    }

    public function detalle($id = null)
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . HTTP_BASE . '/?c= Auth&a=login');
            exit;
        }

        $id = $id ?? $_GET['id'];
        $venta = $this->ventaModel->getById($id);

        if (!$venta) {
            $_SESSION['error'] = 'Venta no encontrada';
            header('Location: ' . HTTP_BASE . '/?c=VentasController&a=index');
            exit;
        }

        
        if ($_SESSION['usuario']['rol'] != 'ADMINISTRADOR' && $venta['id_usuario'] != $_SESSION['usuario']['id_usuario']) {
            $_SESSION['error'] = 'No tiene permisos para ver esta venta';
            header('Location: ' . HTTP_BASE . '/?c=VentasController&a=index');
            exit;
        }

        
        $detalles = $this->detalleVentaModel->getBySaleId($id);

        include ROOT_VIEW . '/client/ventas/detalle.php';
    }

    public function factura($id = null)
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $id = $id ?? $_GET['id'];
        $venta = $this->ventaModel->getById($id);

        if (!$venta) {
            $_SESSION['error'] = 'Factura no encontrada';
            header('Location: ' . HTTP_BASE . '/?c=VentasController&a=index');
            exit;
        }

        
        if ($_SESSION['usuario']['rol'] != 'ADMINISTRADOR' && $venta['id_usuario'] != $_SESSION['usuario']['id_usuario']) {
            $_SESSION['error'] = 'No tienes permisos para ver esta factura';
            header('Location: ' . HTTP_BASE . '/?c=VentasController&a=index');
            exit;
        }

        
        $detalles = $this->detalleVentaModel->getBySaleId($id);

        include ROOT_VIEW . '/client/ventas/factura.php';
    }

    public function reporte()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c= Auth&a=login');
            exit;
        }

        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');

        $ventas = $this->ventaModel->getSalesByDate($fechaInicio, $fechaFin);
        $resumenVentas = $this->detalleVentaModel->getSalesSummary();

        include ROOT_VIEW . '/ventas/reporte.php';
    }

    
    public function crear()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'CLIENTE') {
            header('Location: ' . HTTP_BASE . '/?c= Auth&a=login');
            exit;
        }

        
        require_once ROOT_CORE . '/../models/CarritoModel.php';
        require_once ROOT_CORE . '/../models/InventarioModel.php';

        $carritoModel = new CarritoModel();
        $inventarioModel = new InventarioModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $carrito = $carritoModel->getByUser($_SESSION['usuario']['id_usuario']);

            if (empty($carrito)) {
                $_SESSION['error'] = 'El carrito está vacío';
                header('Location: ' . HTTP_BASE . '/?c=CarritoController&a=index');
                exit;
            }

            
            $subtotal = 0;
            foreach ($carrito as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
            }

            $descuento = 0;
            $impuesto = $subtotal * 0.13; // 13% de impuesto
            $total = $subtotal - $descuento + $impuesto;

            
            $ventaData = [
                'id_usuario' => $_SESSION['usuario']['id_usuario'],
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'impuesto' => $impuesto,
                'total' => $total,
                'metodo_pago' => $_POST['metodo_pago'],
                'estado_pago' => 'PAGADO'
            ];

            $idVenta = $this->ventaModel->create($ventaData);

            if ($idVenta) {
                
                foreach ($carrito as $item) {
                    $detalleData = [
                        'id_venta' => $idVenta,
                        'id_producto' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio'],
                        'subtotal' => $item['precio'] * $item['cantidad']
                    ];

                    $this->detalleVentaModel->create($detalleData);

                    // Actualizar inventario
                    $inventarioModel->subtractStock($item['id_producto'], $item['cantidad']);
                }

                // Limpiar carrito
                $carritoModel->clearCart($_SESSION['usuario']['id_usuario']);

                $_SESSION['success'] = '¡Compra realizada exitosamente! Número de pedido: ' . $idVenta;
                header('Location: ' . HTTP_BASE . '/?c=VentasController&a=detalle&id=' . $idVenta);
                exit;
            } else {
                $_SESSION['error'] = 'Error al procesar la venta';
                header('Location: ' . HTTP_BASE . '/?c=CarritoController&a=index');
                exit;
            }
        } else {
            
            $carrito = $carritoModel->getByUser($_SESSION['usuario']['id_usuario']);

            if (empty($carrito)) {
                $_SESSION['error'] = 'Tu carrito está vacío';
                header('Location: ' . HTTP_BASE . '/?c=CarritoController&a=index');
                exit;
            }

            include ROOT_VIEW . '/client/ventas/crear.php';
        }
    }
}
