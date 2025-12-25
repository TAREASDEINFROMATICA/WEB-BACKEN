<?php
require_once ROOT_CORE . '/Model.php';

class CarritoModel extends Model
{
    protected $table = 'carrito';

    public function __construct()
    {
        parent::__construct();
    }

    public function getByUser($userId)
    {
        $sql = "SELECT c.*, p.nombre, p.precio, p.imagen_url, p.pulgadas, p.resolucion, p.smart_tv 
                FROM {$this->table} c
                JOIN producto p ON c.id_producto = p.id_producto
                WHERE c.id_usuario = ? AND p.estado = 1";
        $stmt = $this->executeQuery($sql, [$userId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function addToCart($userId, $productId, $cantidad = 1)
    {
        $sqlCheck = "SELECT * FROM {$this->table} WHERE id_usuario = ? AND id_producto = ?";
        $stmtCheck = $this->executeQuery($sqlCheck, [$userId, $productId]);
        $existing = $stmtCheck ? $stmtCheck->fetch(PDO::FETCH_ASSOC) : false;

        if ($existing) {
            $sql = "UPDATE {$this->table} SET cantidad = cantidad + ? WHERE id_carrito = ?";
            return $this->executeQuery($sql, [$cantidad, $existing['id_carrito']]);
        } else {
            $sql = "INSERT INTO {$this->table} (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)";
            return $this->executeQuery($sql, [$userId, $productId, $cantidad]);
        }
    }

    public function updateQuantity($cartId, $cantidad)
    {
        $sql = "UPDATE {$this->table} SET cantidad = ? WHERE id_carrito = ?";
        return $this->executeQuery($sql, [$cantidad, $cartId]);
    }

    
    public function actualizarCantidadSumar($userId, $productId, $cantidad)
    {
        $sql = "UPDATE {$this->table} SET cantidad = cantidad + ? WHERE id_usuario = ? AND id_producto = ?";
        return $this->executeQuery($sql, [$cantidad, $userId, $productId]);
    }

    
    public function productoEnCarrito($userId, $productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_usuario = ? AND id_producto = ?";
        $stmt = $this->executeQuery($sql, [$userId, $productId]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function removeFromCart($cartId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_carrito = ?";
        return $this->executeQuery($sql, [$cartId]);
    }

    public function clearCart($userId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_usuario = ?";
        return $this->executeQuery($sql, [$userId]);
    }

    
    public function getCountByUser($userId)
    {
        $sql = "SELECT SUM(cantidad) as total_items FROM {$this->table} WHERE id_usuario = ?";
        $stmt = $this->executeQuery($sql, [$userId]);
        $result = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $result ? $result['total_items'] : 0;
    }
}