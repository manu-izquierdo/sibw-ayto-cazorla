<?php
    date_default_timezone_set('Europe/Madrid'); // Zona horaria en el motor de PHP

    $host = "lamp-mysql8";
    $db   = "sibw";
    $user = "manu_sibw";
    $pass = "practica3";

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Error crítico: No se pudo conectar a la base de datos.");
    }
    
    // Arreglo del desfase de 2 horas
    $mysqli->query("SET time_zone = '+02:00'");

    // Forzar codificación a UTF-8 para evitar problemas
    $mysqli->set_charset("utf8mb4");
?>