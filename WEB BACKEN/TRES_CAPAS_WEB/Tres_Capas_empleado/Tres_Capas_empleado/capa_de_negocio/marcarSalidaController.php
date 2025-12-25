<?php
session_start();

require_once "../capa_de_datos/db.php";
require_once "../capa_de_negocio/funciones.php";

checkAuth();

$id = $_SESSION["id_empleado"];
$msg = "";
$cls = "";

$checkSalida = $conn->prepare("
    SELECT hora FROM salidas 
    WHERE id_empleado=:id AND fecha = CURDATE()
    LIMIT 1
");
$checkSalida->execute([":id"=>$id]);
$yaSalida = $checkSalida->fetchColumn();

$checkEntrada = $conn->prepare("
    SELECT hora FROM ingresos 
    WHERE id_empleado=:id AND fecha = CURDATE()
    LIMIT 1
");
$checkEntrada->execute([":id"=>$id]);
$siEntrada = $checkEntrada->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$siEntrada) {
        $msg = "No puedes registrar salida sin haber registrado entrada hoy.";
        $cls = "warn"; 
    } elseif ($yaSalida) {
        $msg = "Ya registraste tu salida hoy (" . date("H:i:s", strtotime($yaSalida)) . ")";
        $cls = "warn"; 
    } else {
        $stmt = $conn->prepare("
            INSERT INTO salidas (id_empleado, fecha, hora)
            VALUES (:id, CURDATE(), CURTIME())
        ");
        $stmt->execute([":id"=>$id]);
        $msg = "Salida registrada a las " . date("H:i:s");
        $cls = "ok"; 
    }
}
$hora_servidor = date("H:i:s");
?>
