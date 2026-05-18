<?php
    // Iniciamos la sesión antes de cualquier salida al navegador
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    date_default_timezone_set('Europe/Madrid');

    // Conexión no persistente
    function conectar() {
        $host = "lamp-mysql8";
        $db   = "sibw";
        $user = "manu_sibw";
        $pass = "practica3";

        $mysqli = new mysqli($host, $user, $pass, $db);
    
        // Si la conexión falla, el script se detiene y muestra un error.
        if ($mysqli->connect_error) {
            die("Error crítico: No se pudo conectar a la base de datos.");
        }
 
        $mysqli->query("SET time_zone = '+02:00'");
        $mysqli->set_charset("utf8mb4");
 
        return $mysqli;
    }
?>