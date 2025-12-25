<?php
require_once 'Conectar.php';

class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $conectar = new Conectar();
        $this->db = $conectar->conexionPDO();
    }
    
    protected function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error en consulta: " . $e->getMessage());
            return false;
        }
    }
}
?>