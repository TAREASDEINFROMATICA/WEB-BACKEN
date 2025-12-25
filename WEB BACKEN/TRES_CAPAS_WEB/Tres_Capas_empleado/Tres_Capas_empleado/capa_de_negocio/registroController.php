<?php
require_once "../capa_de_datos/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"] ?? "";
    $pass    = password_hash($_POST["contrasena"] ?? "", PASSWORD_DEFAULT);
    $nombre  = $_POST["nombre"] ?? "";
    $apPat   = $_POST["apellido_paterno"] ?? "";
    $apMat   = $_POST["apellido_materno"] ?? "";
    $turno   = $_POST["turno"] ?? "Completo";

    $stmt = $conn->prepare("INSERT INTO empleados (usuario, contrasena, nombre, apellido_paterno, apellido_materno, turno) 
                            VALUES (:usuario, :pass, :nombre, :apPat, :apMat, :turno)");

    $stmt->execute([
        ":usuario" => $usuario,
        ":pass"    => $pass,
        ":nombre"  => $nombre,
        ":apPat"   => $apPat,
        ":apMat"   => $apMat,
        ":turno"   => $turno
    ]);


    header("Location: registro.php");
    exit();
}
?>
