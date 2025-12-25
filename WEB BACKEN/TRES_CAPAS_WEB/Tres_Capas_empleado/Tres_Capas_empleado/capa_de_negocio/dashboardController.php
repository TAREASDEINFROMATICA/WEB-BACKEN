<?php
session_start();
if (!isset($_SESSION["id_empleado"])) {
    header("Location: index.php");
    exit();
}

require_once "../capa_de_datos/empleadoModel.php"; 

$id = $_SESSION["id_empleado"];
$empleado = obtenerEmpleado($id); 
$nombre_completo = $empleado["nombre"] . " " . $empleado["apellido_paterno"] . " " . $empleado["apellido_materno"];
$estado_txt = $empleado["habilitado"] ? "Habilitado" : "Deshabilitado";
$estado_cls = $empleado["habilitado"] ? "badge-ok" : "badge-off";
$turno = $empleado["turno"];

$hora_servidor = date("H:i:s");
$fecha_hoy = date("Y-m-d");
?>
