<?php
require_once ROOT_CORE . '/Model.php';

class CategoriaModel extends Model
{
    protected $table = 'categoria';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre_categoria";
        $stmt = $this->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_categoria = ?";
        $stmt = $this->executeQuery($sql, [$id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function create($nombre)
    {
        $sql = "INSERT INTO {$this->table} (nombre_categoria) VALUES (?)";
        return $this->executeQuery($sql, [$nombre]);
    }

    public function update($id, $nombre)
    {
        $sql = "UPDATE {$this->table} SET nombre_categoria = ? WHERE id_categoria = ?";
        return $this->executeQuery($sql, [$nombre, $id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_categoria = ?";
        return $this->executeQuery($sql, [$id]);
    }
}
