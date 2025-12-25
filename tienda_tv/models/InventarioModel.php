<?php
require_once ROOT_CORE . '/Model.php';

class InventarioModel extends Model
{
    protected $table = 'inventario';

    public function __construct()
    {
        parent::__construct();
    }

    public function getByProductId($productId)
    {
        $sql = "SELECT i.*, p.nombre as producto_nombre 
                FROM {$this->table} i 
                JOIN producto p ON i.id_producto = p.id_producto 
                WHERE i.id_producto = ?";
        $stmt = $this->executeQuery($sql, [$productId]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function getAllWithProducts()
    {
        $sql = "SELECT i.*, p.nombre as producto_nombre, p.codigo_sku 
                FROM {$this->table} i 
                JOIN producto p ON i.id_producto = p.id_producto 
                WHERE p.estado = 1";
        $stmt = $this->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function updateStock($productId, $nuevoStock)
    {
        $sql = "UPDATE {$this->table} SET stock_actual = ? WHERE id_producto = ?";
        return $this->executeQuery($sql, [$nuevoStock, $productId]);
    }

    public function addStock($productId, $cantidad)
    {
        $sql = "UPDATE {$this->table} SET stock_actual = stock_actual + ? WHERE id_producto = ?";
        return $this->executeQuery($sql, [$cantidad, $productId]);
    }

    public function subtractStock($productId, $cantidad)
    {
        $sql = "UPDATE {$this->table} SET stock_actual = stock_actual - ? WHERE id_producto = ?";
        return $this->executeQuery($sql, [$cantidad, $productId]);
    }

    public function getLowStock()
    {
        $sql = "SELECT i.*, p.nombre as producto_nombre 
                FROM {$this->table} i 
                JOIN producto p ON i.id_producto = p.id_producto 
                WHERE i.stock_actual <= i.stock_minimo AND p.estado = 1";
        $stmt = $this->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (id_producto, stock_actual, stock_minimo, stock_maximo, actualizado_en) 
                VALUES (?, ?, ?, ?, ?)";

        return $this->executeQuery($sql, [
            $data['id_producto'],
            $data['stock_actual'],
            $data['stock_minimo'],
            $data['stock_maximo'],
            $data['actualizado_en']
        ]);
    }
    public function delete($productId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_producto = ?";
        return $this->executeQuery($sql, [$productId]);
    }
}
