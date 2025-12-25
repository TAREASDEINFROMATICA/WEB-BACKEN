<?php
if (!defined('ROOT_CORE')) {
    require_once __DIR__ . '/../config/config.php';
}
require_once ROOT_CORE . '/Model.php';

class UsuarioModel extends Model
{
    protected $table = 'usuario';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 1";
        $stmt = $this->executeQuery($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_usuario = ? AND estado = 1";
        $stmt = $this->executeQuery($sql, [$id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function getByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE correo = ? AND estado = 1";
        $stmt = $this->executeQuery($sql, [$email]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (nombre, ci, telefono, direccion, correo, username, password_hash, rol, fecha_nacimiento, genero) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->executeQuery($sql, [
            $data['nombre'],
            $data['ci'],
            $data['telefono'],
            $data['direccion'],
            $data['correo'],
            $data['username'],
            $data['password_hash'],
            $data['rol'],
            $data['fecha_nacimiento'],
            $data['genero']
        ]);
    }
    public function update($id, $data)
    {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            if ($value !== '' && $value !== null) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        if (empty($fields)) {
            error_log("No hay campos válidos para actualizar");
            return false;
        }

        $params[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id_usuario = ?";
        error_log("SQL Update: " . $sql);
        error_log("Params: " . print_r($params, true));
        return $this->executeQuery($sql, $params);
    }
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id_usuario = ?";
        return $this->executeQuery($sql, [$id]);
    }
}
