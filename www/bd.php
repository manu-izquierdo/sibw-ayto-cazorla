<?php
    date_default_timezone_set('Europe/Madrid');

    $host = "lamp-mysql8";
    $db   = "sibw";
    $user = "manu_sibw";
    $pass = "practica3";

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Error crítico: No se pudo conectar a la base de datos.");
    }
    
    $mysqli->query("SET time_zone = '+02:00'");

    // Opcional pero recomendado: Forzar la codificación a UTF-8 para evitar problemas con tildes
    $mysqli->set_charset("utf8mb4");
?>