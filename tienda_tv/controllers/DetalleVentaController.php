<?php
require_once ROOT_CORE . '/Model.php';
require_once ROOT_CORE . '/../models/DetalleVentaModel.php';

class DetalleVentaController
{
    private $detalleVentaModel;

    public function __construct()
    {
        $this->detalleVentaModel = new DetalleVentaModel();
    }

    public function porVenta($idVenta = null)
    {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $idVenta = $idVenta ?? $_GET['id_venta'];
        $detalles = $this->detalleVentaModel->getBySaleId($idVenta);
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode($detalles);
            exit;
        }

        include ROOT_VIEW . '/detalle_venta/index.php';
    }

    public function productosMasVendidos()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 'ADMINISTRADOR') {
            header('Location: ' . HTTP_BASE . '/?c=Auth&a=login');
            exit;
        }

        $productosMasVendidos = $this->detalleVentaModel->getSalesSummary();
        include ROOT_VIEW . '/detalle_venta/productos_mas_vendidos.php';
    }
}
