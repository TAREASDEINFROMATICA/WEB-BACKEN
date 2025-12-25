<?php
require_once "../capa_de_negocio/reportePagosController.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos</title>
    <link rel="stylesheet" href="../capa_de_presentacion/public/css/style.css">
</head>
<body>
    <h2>Reporte de Pagos</h2>
    <table>
        <tr>
            <th>Empleado</th>
            <th>Turno</th>
            <th>Fecha</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Sueldo Base</th>
            <th>Bono</th>
            <th>Descuento</th>
            <th>Total</th>
        </tr>
        <?php
        foreach ($resultados as $r) {
            echo "<tr>
                    <td>{$r['nombre']} {$r['apellido_paterno']}</td>
                    <td>{$r['turno']}</td>
                    <td>{$r['fecha']}</td>
                    <td>{$r['hora_entrada']}</td>
                    <td>{$r['hora_salida']}</td>
                    <td>{$r['sueldo_base']}</td>
                    <td>{$r['bono']}</td>
                    <td>{$r['descuento']}</td>
                    <td>{$r['total']}</td>
                  </tr>";
        }
        ?>
    </table>
    <p style="text-align:center;"><a href="dashboard.php">Volver</a></p>
</body>
</html>
