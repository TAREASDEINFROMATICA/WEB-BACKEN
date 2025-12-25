<?php
require_once "../capa_de_datos/db.php";

$sql = "SELECT e.id_empleado, e.nombre, e.apellido_paterno, e.turno,
               i.fecha, i.hora AS hora_entrada, s.hora AS hora_salida
        FROM empleados e
        JOIN ingresos i ON e.id_empleado = i.id_empleado
        JOIN salidas s ON e.id_empleado = s.id_empleado AND i.fecha = s.fecha
        ORDER BY e.id_empleado, i.fecha";

$stmt = $conn->query($sql);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultados as &$r) {
    $entrada = strtotime($r["hora_entrada"]);
    $salida  = strtotime($r["hora_salida"]);
    $horas_trabajadas = ($salida - $entrada) / 3600;

    switch ($r["turno"]) {
        case "Completo": $sueldo_base = 100; break;
        case "Medio Turno": $sueldo_base = 50; break;
        case "Fines de Semana": $sueldo_base = 30; break;
        default: $sueldo_base = 0;
    }

    $bono = ($horas_trabajadas >= 8) ? $sueldo_base * 0.05 : 0;
    $descuento = ($horas_trabajadas < 8) ? $sueldo_base * 0.10 : 0;
    $total = $sueldo_base + $bono - $descuento;

    $r['sueldo_base'] = $sueldo_base;
    $r['bono'] = $bono;
    $r['descuento'] = $descuento;
    $r['total'] = $total;
}
?>
