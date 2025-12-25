<?php
// PHP puede generar un valor que luego será usado por JavaScript
$datos_servidor = "Mensaje generado por PHP el " . date("d/m/Y H:i:s");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ejemplo de Almacenamiento Web</title>
</head>
<body>
    <h1>Almacenamiento Local (localStorage) y SessionStorage</h1>
    <p>Trabajado con JavaScript y no así con PHP:</p>
    <input type="text" id="nuevoDato" placeholder="Escribe algo aquí">
    <button onclick="guardarDato()">Guardado en localStore</button>

    <p>Dato del Servidor (PHP): <span id="phpDato"><?php echo $datos_servidor; ?></span></p>
    <p>Dato Guardado en LocalStorage: <span id="datoLocalStorage"></span></p>
    <p>Dato Guardado en SessionStorage: <span id="datoSessionStorage"></span></p>

    <script>
        const CLAVE_LOCAL = "nombreUsuario";
        const CLAVE_SESSION = "ultimaVisita";

        // 1.- FUNCIÓN PARA GUARDAR EN LOCALSTORAGE ----------------------
        function guardarDato() {
            const valor = document.getElementById("nuevoDato").value;
            if (valor) {
                // localStorage persiste incluso si cierras el navegador
                localStorage.setItem(CLAVE_LOCAL, valor);
                alert('Dato "' + valor + '" guardado en LocalStorage.');
                mostrarDatos(); // Actualizar la pantalla
            }
        }

        function mostrarDatos() {
            document.getElementById("datoLocalStorage").innerHTML = localStorage.getItem(CLAVE_LOCAL);
            document.getElementById("datoSessionStorage").innerHTML = sessionStorage.getItem(CLAVE_SESSION);
        }
    </script>
</body>
</html>
