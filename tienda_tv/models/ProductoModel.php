<?php
require_once ROOT_CORE . '/Model.php';

class ProductoModel extends Model
{
    protected $table = 'producto';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT p.id_producto
            FROM {$this->table} p
            WHERE p.estado = 1
            ORDER BY p.id_producto DESC";

        $stmt = $this->executeQuery($sql);
        $ids = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];

        if (empty($ids)) {
            return [];
        }
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT p.*, m.nombre_marca, c.nombre_categoria, pr.nombre as nombre_proveedor 
            FROM {$this->table} p
            LEFT JOIN marca m ON p.id_marca = m.id_marca
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
            WHERE p.id_producto IN ($placeholders)
            ORDER BY p.id_producto DESC";

        $stmt = $this->executeQuery($sql, $ids);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    public function getById($id)
    {
        $sql = "SELECT p.*, m.nombre_marca, c.nombre_categoria, pr.nombre as nombre_proveedor 
            FROM {$this->table} p
            LEFT JOIN marca m ON p.id_marca = m.id_marca
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
            WHERE p.id_producto = ? AND p.estado = 1";
        $stmt = $this->executeQuery($sql, [$id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }
    public function create($data)
    {
        if (!empty($data['codigo_sku'])) {
            $existing = $this->getBySku($data['codigo_sku']);
            if ($existing) {
                throw new Exception('El código SKU ya existe');
            }
        }

        $sql = "INSERT INTO {$this->table} 
            (nombre, descripcion, precio, id_marca, id_categoria, id_proveedor, 
             codigo_sku, imagen_url, pulgadas, resolucion, smart_tv, hdmi_puertos, usb_puertos, garantia_meses) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->executeQuery($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['id_marca'],
            $data['id_categoria'],
            $data['id_proveedor'],
            $data['codigo_sku'],
            $data['imagen_url'],
            $data['pulgadas'],
            $data['resolucion'],
            $data['smart_tv'],
            $data['hdmi_puertos'],
            $data['usb_puertos'],
            $data['garantia_meses']
        ]);
    }
    public function getBySku($sku)
    {
        $sql = "SELECT * FROM {$this->table} WHERE codigo_sku = ?";
        $stmt = $this->executeQuery($sql, [$sku]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }
    private function checkForeignKey($table, $id)
    {
        $sql = "SELECT 1 FROM $table WHERE id_$table = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() !== false;
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                nombre=?, descripcion=?, precio=?, id_marca=?, id_categoria=?, id_proveedor=?,
                codigo_sku=?, imagen_url=?, pulgadas=?, resolucion=?, smart_tv=?, 
                hdmi_puertos=?, usb_puertos=?, garantia_meses=?
                WHERE id_producto=?";

        return $this->executeQuery($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['id_marca'],
            $data['id_categoria'],
            $data['id_proveedor'],
            $data['codigo_sku'],
            $data['imagen_url'],
            $data['pulgadas'],
            $data['resolucion'],
            $data['smart_tv'],
            $data['hdmi_puertos'],
            $data['usb_puertos'],
            $data['garantia_meses'],
            $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM producto WHERE id_producto = ?";
        return $this->executeQuery($sql, [$id]);
    }

    public function search($term)
    {
        $sql = "SELECT p.*, m.nombre_marca, c.nombre_categoria 
                FROM {$this->table} p
                LEFT JOIN marca m ON p.id_marca = m.id_marca
                LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE (p.nombre LIKE ? OR p.descripcion LIKE ?) AND p.estado = 1";
        $stmt = $this->executeQuery($sql, ["%$term%", "%$term%"]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    
    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }
}
