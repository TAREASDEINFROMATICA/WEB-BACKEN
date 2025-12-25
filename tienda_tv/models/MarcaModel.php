<?php
require_once ROOT_CORE . '/Model.php';

class MarcaModel extends Model
{
    protected $table = 'marca';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre_marca";
        $stmt = $this->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_marca = ?";
        $stmt = $this->executeQuery($sql, [$id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function create($nombre)
    {
        $sql = "INSERT INTO {$this->table} (nombre_marca) VALUES (?)";
        return $this->executeQuery($sql, [$nombre]);
    }

    public function update($id, $nombre)
    {
        $sql = "UPDATE {$this->table} SET nombre_marca = ? WHERE id_marca = ?";
        return $this->executeQuery($sql, [$nombre, $id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_marca = ?";
        return $this->executeQuery($sql, [$id]);
    }
}
