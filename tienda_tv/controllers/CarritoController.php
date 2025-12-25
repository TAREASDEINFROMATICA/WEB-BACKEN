<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/CarritoModel.php';
require_once ROOT_CORE . '/../models/ProductoModel.php';
require_once ROOT_CORE . '/../models/InventarioModel.php';

class CarritoController
{
    private $carritoModel;
    private $productoModel;
    private $inventarioModel;

    public function __construct()
    {
        $this->carritoModel = new CarritoModel();
        $this->productoModel = new ProductoModel();
        $this->inventarioModel = new InventarioModel();
    }

    public function index()
    {
        
        if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] == 'CLIENTE') {
            $carrito = $this->carritoModel->getByUser($_SESSION['usuario']['id_usuario']);
        } else {
            $carrito = []; 
        }
        include ROOT_VIEW . '/client/carrito/index.php';
    }

    public function agregar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoId = $_POST['producto_id'];
            $cantidad = $_POST['cantidad'] ?? 1;

            
            $inventario = $this->inventarioModel->getByProductId($productoId);
            if ($inventario && $inventario['stock_actual'] < $cantidad) {
                if (isset($_POST['ajax'])) {
                    echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
                } else {
                    $_SESSION['error'] = 'Stock insuficiente';
                    header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
                }
                exit;
            }

            if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] == 'CLIENTE') {
                if ($this->carritoModel->addToCart($_SESSION['usuario']['id_usuario'], $productoId, $cantidad)) {
                    if (isset($_POST['ajax'])) {
                        echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
                    } else {
                        $_SESSION['success'] = 'Producto agregado al carrito';
                        header('Location: ' . HTTP_BASE . '/?c=CarritoController&a=index');
                    }
                } else {
                    if (isset($_POST['ajax'])) {
                        echo json_encode(['success' => false, 'message' => 'Error al agregar al carrito']);
                    } else {
                        $_SESSION['error'] = 'Error al agregar al carrito';
                        header('Location: ' . HTTP_BASE . '/?c=ProductosController&a=index');
                    }
                }
            } else {
                // Usuario no logueado - responder para LocalStorage
                if (isset($_POST['ajax'])) {
                    // Obtener información del producto para el carrito temporal
                    $producto = $this->productoModel->getById($productoId);
                    if ($producto) {
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Producto agregado al carrito temporal',
                            'para_localstorage' => true,
                            'producto' => [
                                'id_producto' => $producto['id_producto'],
                                'nombre' => $producto['nombre'],
                                'precio' => $producto['precio'],
                                'cantidad' => $cantidad,
                                'imagen_url' => $producto['imagen_url'],
                                'stock_actual' => $inventario['stock_actual']
                            ]
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                    }
                } else {
                    $_SESSION['error'] = 'Debe iniciar sesión para agregar productos al carrito';
                    header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
                }
            }
        }
    }

    public function sincronizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] == 'CLIENTE') {
            $input = json_decode(file_get_contents('php://input'), true);
            $productos = $input['productos'] ?? [];
            $idUsuario = $_SESSION['usuario']['id_usuario'];

            $resultados = [];
            $errores = [];

            foreach ($productos as $producto) {
                $inventario = $this->inventarioModel->getByProductId($producto['id_producto']);
                
                if (!$inventario || $inventario['stock_actual'] < $producto['cantidad']) {
                    $errores[] = "Stock insuficiente para: " . $producto['nombre'];
                    continue;
                }

                $existe = $this->carritoModel->productoEnCarrito($idUsuario, $producto['id_producto']);
                
                if ($existe) {
                    // Actualizar cantidad sumando
                    if ($this->carritoModel->actualizarCantidadSumar($idUsuario, $producto['id_producto'], $producto['cantidad'])) {
                        $resultados[] = "Producto actualizado: " . $producto['nombre'];
                    } else {
                        $errores[] = "Error al actualizar: " . $producto['nombre'];
                    }
                } else {
                    // Agregar nuevo producto
                    if ($this->carritoModel->addToCart($idUsuario, $producto['id_producto'], $producto['cantidad'])) {
                        $resultados[] = "Producto agregado: " . $producto['nombre'];
                    } else {
                        $errores[] = "Error al agregar: " . $producto['nombre'];
                    }
                }
            }

            echo json_encode([
                'success' => count($errores) === 0,
                'message' => 'Sincronización completada',
                'resultados' => $resultados,
                'errores' => $errores
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        }
    }

    public function actualizar()
    {
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $carritoId = $_POST['carrito_id'];
            $cantidad = $_POST['cantidad'];

            if ($this->carritoModel->updateQuantity($carritoId, $cantidad)) {
                echo json_encode(['success' => true, 'message' => 'Carrito actualizado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar carrito']);
            }
        }
    }

    public function eliminar($id = null)
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'CLIENTE') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $id = $id ?? $_GET['id'];

        if ($this->carritoModel->removeFromCart($id)) {
            $_SESSION['success'] = 'Producto eliminado del carrito';
        } else {
            $_SESSION['error'] = 'Error al eliminar del carrito';
        }

        header('Location: ' . HTTP_BASE . '/?c=CarritoController&a=index');
    }

    public function limpiar()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'CLIENTE') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        if ($this->carritoModel->clearCart($_SESSION['usuario']['id_usuario'])) {
            $_SESSION['success'] = 'Carrito limpiado correctamente';
        } else {
            $_SESSION['error'] = 'Error al limpiar carrito';
        }

        header('Location: ' . HTTP_BASE . '/?c=CarritoController&a=index');
    }

    // NUEVO MÉTODO: Obtener información del producto para LocalStorage
    public function obtenerProducto($id)
    {
        $producto = $this->productoModel->getById($id);
        if ($producto) {
            $inventario = $this->inventarioModel->getByProductId($id);
            echo json_encode([
                'success' => true,
                'producto' => [
                    'id_producto' => $producto['id_producto'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'imagen_url' => $producto['imagen_url'],
                    'stock_actual' => $inventario ? $inventario['stock_actual'] : 0
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        }
    }
}