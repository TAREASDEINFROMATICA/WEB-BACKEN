<?php
require_once ROOT_CORE . '/Model.php';

class DetalleVentaModel extends Model
{
    protected $table = 'detalle_venta';

    public function __construct()
    {
        parent::__construct();
    }

    public function getBySaleId($saleId)
    {
        $sql = "SELECT d.*, p.nombre as producto_nombre 
                FROM {$this->table} d
                JOIN producto p ON d.id_producto = p.id_producto
                WHERE d.id_venta = ?";
        $stmt = $this->executeQuery($sql, [$saleId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                VALUES (?, ?, ?, ?, ?)";

        return $this->executeQuery($sql, [
            $data['id_venta'],
            $data['id_producto'],
            $data['cantidad'],
            $data['precio_unitario'],
            $data['subtotal']
        ]);
    }

    public function getSalesSummary()
    {
        $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido, SUM(dv.subtotal) as ingreso_total
                FROM {$this->table} dv
                JOIN producto p ON dv.id_producto = p.id_producto
                GROUP BY p.id_producto
                ORDER BY total_vendido DESC";
        $stmt = $this->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}
