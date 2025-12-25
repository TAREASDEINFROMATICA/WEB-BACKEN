<?php
require_once 'config/global.php';
session_start();
if (!isset($_GET['c']) && !isset($_GET['a'])) {
    header('Location: ' . HTTP_BASE . '/?c=Auth&a=index');
    exit;
}
$controller = isset($_GET['c']) ? $_GET['c'] : CONTROLADOR_DEFECTO;
$action = isset($_GET['a']) ? $_GET['a'] : ACCION_DEFECTO;
if (!empty($controller) && $controller != CONTROLADOR_DEFECTO) {

    if (substr($controller, -10) !== 'Controller') {
        $controller .= 'Controller';
    }
}
$controller_file = ROOT_DIR . '/controllers/' . $controller . '.php';
if (file_exists($controller_file)) {
    require_once $controller_file;

    if (class_exists($controller)) {
        $app = new $controller();
        if (method_exists($app, $action)) {
            $app->$action();
        } else {
            echo "<h1>Error 404 - Acción no encontrada</h1>";
            echo "<p>La acción '$action' no existe en $controller</p>";
        }
    } else {
        echo "<h1>Error - Clase no encontrada</h1>";
        echo "<p>La clase $controller no existe en $controller_file</p>";
    }
} else {
    echo "<h1>Error 404 - Controlador no encontrado</h1>";
    echo "<p>No se encontró: $controller_file</p>";
}
