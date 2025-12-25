<?php
// PHP puede generar un valor que luego será usado por JavaScript
$dato_servidor = "Mensaje generado por PHP el " . date("H:i:s");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Ejemplo de Almacenamiento Web</title>
</head>

<body>
    <h1>Almacenamiento Local (localStorage)</h1>
    <p>Trabajando con JavaScript y no así con PHP</p>
    <input type="text" id="nuevoDato" placeholder="Escribe algo aquí">
    <button onclick="guardarDato()">Guardar en LocalStorage</button>

    <h2>Dato del Servidor (PHP): <?php echo $dato_servidor; ?></h2>
    <p>Dato Guardado en localStorage: <b id="datoLocalStorage"></b></p>

    <h1>Almacenamiento en Session Storage</h1>
    <p>Guardando datos en Session Storage:</p>

    <script>
        // Constantes para las claves de almacenamiento
        const CLAVE_LOCAL = "nombreUsuario";
        const CLAVE_SESSION = "ultimaVisita";

        // Función para guardar en localStorage
        function guardarDato() {
            var valor = document.getElementById("nuevoDato").value;
            // localStorage persiste incluso si cierras el navegador
            localStorage.setItem(CLAVE_LOCAL, valor);
            alert("Dato '" + valor + "' guardado en localStorage.");
            mostrarDatos(); // Actualizar la pantalla
        }

        // --- 2. FUNCIÓN PARA LEER Y MOSTRAR DATOS ---
        function mostrarDatos() {
            // Leer desde LocalStorage
            const localData = localStorage.getItem(CLAVE_LOCAL);
            document.getElementById('datoLocalStorage').textContent =
                localData ? localData : "Aún no hay datos guardados.";

            // Leer desde sessionStorage
            const sessionData = sessionStorage.getItem(CLAVE_SESSION);
            document.getElementById('datoSessionStorage').textContent =
                sessionData ? sessionData : "No hay datos en sesión.";
        }

        // --- 3. INICIALIZACIÓN (Guardar algo en sessionStorage al cargar) ---
        // sessionStorage se borra cuando cierras la pestaña o el navegador
        sessionStorage.setItem(CLAVE_SESSION, "Página visitada a las: " +
            new Date().toLocaleTimeString());
        // Cargar los datos al iniciar la página
        mostrarDatos();
    </script>

</body>

</html>
