<?php
require_once "../capa_de_datos/db.php";


function obtenerEmpleado($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT nombre, apellido_paterno, apellido_materno, turno, habilitado FROM empleados WHERE id_empleado = :id");
    $stmt->execute([":id" => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC); 
}
?>
