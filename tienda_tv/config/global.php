<?php

define("RUTA_BASE", $_SERVER['DOCUMENT_ROOT'] . "/");
define("HTTP_BASE", "http://localhost/tienda_tv");

define('ROOT_DIR', RUTA_BASE . 'tienda_tv');
define('ROOT_CORE', ROOT_DIR . '/core');
define('ROOT_VIEW', ROOT_DIR . '/views'); 
define('ROOT_CONTROLLERS', ROOT_DIR . '/controllers');
define('ROOT_MODELS', ROOT_DIR . '/models');
define('ROOT_CONFIG', ROOT_DIR . '/config');
define('ROOT_UPLOAD', ROOT_DIR . '/uploads');
define('ROOT_REPORT', ROOT_DIR . '/reports');
define('ROOT_PUBLIC', ROOT_DIR . '/public');

define("URL_RESOURCES", HTTP_BASE . "/public/");
define("URL_CSS", HTTP_BASE . "/public/css/");
define("URL_JS", HTTP_BASE . "/public/js/");
define("URL_IMAGES", HTTP_BASE . "/public/images/");

define("CONTROLADOR_DEFECTO", "HomeController"); 
define("ACCION_DEFECTO", "index");

define('SECRET_KEY', 'MIEMPRESA.MBmxKMiasdqweqwefghY7jhvycews3wecv9uij976rd43strpz21');
define('ALGORITHM', 'HS256');
?>