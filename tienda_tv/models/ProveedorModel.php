<?php
require_once ROOT_CORE . '/Model.php';

class ProveedorModel extends Model
{
    protected $table = 'proveedor';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_proveedor = ?";
        $stmt = $this->executeQuery($sql, [$id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (nombre, telefono, direccion, correo) VALUES (?, ?, ?, ?)";
        return $this->executeQuery($sql, [
            $data['nombre'],
            $data['telefono'],
            $data['direccion'],
            $data['correo']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET nombre=?, telefono=?, direccion=?, correo=? WHERE id_proveedor=?";
        return $this->executeQuery($sql, [
            $data['nombre'],
            $data['telefono'],
            $data['direccion'],
            $data['correo'],
            $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_proveedor = ?";
        return $this->executeQuery($sql, [$id]);
    }
}
