<?php
session_start();

require_once "../capa_de_datos/db.php";
require_once "../capa_de_negocio/funciones.php";

checkAuth();

$id = $_SESSION["id_empleado"];
$msg = "";
$cls = "";

$check = $conn->prepare("
    SELECT hora 
    FROM ingresos 
    WHERE id_empleado = :id 
      AND fecha = CURDATE()  -- Si 'fecha' es DATETIME, usar DATE(fecha) = CURDATE()
    LIMIT 1
");
$check->execute([":id" => $id]);
$yaExiste = $check->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($yaExiste) {
        $msg = "Ya registraste tu entrada hoy (" . date("H:i:s", strtotime($yaExiste)) . ")";
        $cls = "warn"; 
    } else {
        $stmt = $conn->prepare("
            INSERT INTO ingresos (id_empleado, fecha, hora)
            VALUES (:id, CURDATE(), CURTIME())
        ");
        $stmt->execute([":id" => $id]);
        $msg = "Entrada registrada a las " . date("H:i:s");
        $cls = "ok"; 
    }
}
$hora_servidor = date("H:i:s");
?>
