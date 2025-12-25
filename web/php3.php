<?php
// --- LÓGICA DE MANEJO DE COOKIES ---

$nombre_cookie = "preferencia_usuario";
$tiempo_expiracion = time() + (86400 * 30); // 86400 segundos = 1 día. La cookie durará 30 días.

// 1. ESTABLECER LA COOKIE
// setcookie() debe llamarse ANTES de enviar cualquier salida al navegador (antes de cualquier HTML).
if (isset($_POST['establecer'])) {
    $valor = htmlspecialchars($_POST['valor_cookie']);
    // setcookie(nombre, valor, expiración, ruta, dominio, seguridad, httponly)
    if (setcookie($nombre_cookie, $valor, $tiempo_expiracion, "/")) {
        $mensaje = "Cookie '$nombre_cookie' establecida con el valor: <b>$valor</b>.";
    } else {
        $mensaje = "Error al establecer la cookie.";
    }
}

// 2. ELIMINAR LA COOKIE
// Para eliminar una cookie, se llama a setcookie() con una fecha de expiración en el pasado.
if (isset($_POST['eliminar'])) {
    if (setcookie($nombre_cookie, "", time() - 3600, "/")) { // -3600 segundos (hace una hora)
        $mensaje = "Cookie '$nombre_cookie' eliminada.";
        // Limpiar el array $_COOKIE para reflejar el cambio inmediatamente
        unset($_COOKIE[$nombre_cookie]);
    } else {
        $mensaje = "Error al eliminar la cookie.";
    }
}

// 3. LEER LA COOKIE
// Las cookies se acceden a través del array superglobal $_COOKIE
$valor_leido = "No establecida";
if (isset($_COOKIE[$nombre_cookie])) {
    $valor_leido = htmlspecialchars($_COOKIE[$nombre_cookie]);
}

// --- FIN DE LA LÓGICA ---

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ejemplo Básico de Cookies con PHP</title>
</head>
<body>
    <h1>Manejo de Cookies con PHP</h1>
    <?php if (isset($mensaje)) { ?>
        <p style="padding: 10px; border: 1px solid #ccc; background-color: #f0f0f0;"><?php echo $mensaje; ?></p>
    <?php } ?>

    <h2>1. Estado Actual de la Cookie</h2>
    <p>Nombre de la Cookie: <b><?php echo $nombre_cookie; ?></b></p>
    <p>Valor Leído ($_COOKIE): <b><?php echo $valor_leido; ?></b></p>

    <hr>

    <h2>2. Establecer / Modificar Cookie</h2>
    <form method="POST">
        <label for="valor_cookie">Nuevo Valor:</label>
        <input type="text" id="valor_cookie" name="valor_cookie" value="Modo Oscuro" required>
        <button type="submit" name="establecer">Establecer Cookie (30 días)</button>
    </form>

    <hr>

    <h2>3. Eliminar Cookie</h2>
    <form method="POST">
        <button type="submit" name="eliminar">Eliminar Cookie</button>
    </form>
</body>
</html>
