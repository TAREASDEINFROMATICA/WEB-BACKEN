<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $usuario = $_POST["usuario"] ?? "";
    $pass    = $_POST["contrasena"] ?? "";

    require_once "../capa_de_datos/db.php"; 

    $stmt = $conn->prepare("SELECT * FROM empleados WHERE usuario = :usuario");
    $stmt->bindParam(":usuario", $usuario);
    $stmt->execute();
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($empleado && password_verify($pass, $empleado["contrasena"])) {
       
        $_SESSION["id_empleado"] = $empleado["id_empleado"];
        $_SESSION["nombre"]      = $empleado["nombre"];
       
        header("Location: dashboard.php");
        exit();
    } else {
       
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
