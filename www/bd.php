<?php
    date_default_timezone_set('Europe/Madrid'); // Zona horaria en el motor de PHP

    $host = "lamp-mysql8";
    $db   = "sibw";
    $user = "manu_sibw";
    $pass = "practica3";

    // Abre una nueva conexión a MySQL usando las credenciales anteriores
    $mysqli = new mysqli($host, $user, $pass, $db);

    // Si la conexión falla, el script se detiene y muestra un error.
    if ($mysqli->connect_error) {
        die("Error crítico: No se pudo conectar a la base de datos.");
    }
    
    // Arreglo desfase de 2 horas
    $mysqli->query("SET time_zone = '+02:00'");

    // Codificación a UTF-8 para evitar problemas
    $mysqli->set_charset("utf8mb4");
?>