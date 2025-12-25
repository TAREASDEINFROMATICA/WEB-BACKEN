<?php
function checkAuth() {
    if (!isset($_SESSION["id_empleado"])) {
        header("Location: index.php");
        exit();
    }
}
function nombreCompleto($emp) {
    return $emp["nombre"] . " " . $emp["apellido_paterno"] . " " . $emp["apellido_materno"];
}
function calcularPago($turno, $horas_trabajadas) {
    switch ($turno) {
        case "Completo": $sueldo_base = 100; break;
        case "Medio Turno": $sueldo_base = 50; break;
        case "Fines de Semana": $sueldo_base = 30; break;
        default: $sueldo_base = 0;
    }

    $bono = ($horas_trabajadas >= 8) ? $sueldo_base * 0.05 : 0;
    $descuento = ($horas_trabajadas < 8) ? $sueldo_base * 0.10 : 0;

    $total = $sueldo_base + $bono - $descuento;

    return [
        "sueldo_base" => $sueldo_base,
        "bono" => $bono,
        "descuento" => $descuento,
        "total" => $total
    ];
}

function formatearHora($hora) {
    return date("H:i", strtotime($hora));
}
function formatearFecha($fecha) {
    return date("d/m/Y", strtotime($fecha));
}
