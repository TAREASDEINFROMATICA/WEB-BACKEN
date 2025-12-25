<?php
$host = "localhost";
$user = "root";     
$pass = "";    
#$pass = "12345"; #mi phpMyadmin 
$db   = "bd_empleado";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    date_default_timezone_set('America/La_Paz'); 
    $conn->exec("SET time_zone = '-04:00'");
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
