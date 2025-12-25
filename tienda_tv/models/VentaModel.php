<?php
require_once ROOT_CORE . '/Model.php';

class VentaModel extends Model
{
    protected $table = 'venta';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT v.*, u.nombre as cliente_nombre 
                FROM {$this->table} v
                JOIN usuario u ON v.id_usuario = u.id_usuario
                ORDER BY v.fecha DESC";
        $stmt = $this->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getById($id)
    {
        $sql = "SELECT v.*, u.nombre as cliente_nombre 
                FROM {$this->table} v
                JOIN usuario u ON v.id_usuario = u.id_usuario
                WHERE v.id_venta = ?";
        $stmt = $this->executeQuery($sql, [$id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (id_usuario, subtotal, descuento, impuesto, total, metodo_pago, estado_pago) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->executeQuery($sql, [
            $data['id_usuario'],
            $data['subtotal'],
            $data['descuento'],
            $data['impuesto'],
            $data['total'],
            $data['metodo_pago'],
            $data['estado_pago']
        ]);

        return $stmt ? $this->db->lastInsertId() : false;
    }
    public function getSalesByDate($fechaInicio, $fechaFin)
    {
        $sql = "SELECT v.*, u.nombre as cliente_nombre 
                FROM {$this->table} v
                JOIN usuario u ON v.id_usuario = u.id_usuario
                WHERE v.fecha BETWEEN ? AND ?
                ORDER BY v.fecha DESC";
        $stmt = $this->executeQuery($sql, [$fechaInicio, $fechaFin]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    public function getSalesByUser($userId)
    {
        $sql = "SELECT * FROM {$this->table} 
            WHERE id_usuario = ? 
            ORDER BY fecha DESC";
        $stmt = $this->executeQuery($sql, [$userId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}
