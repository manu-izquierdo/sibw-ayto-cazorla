<?php
    // Carga Twig y la conexión a la Base de Datos
    require_once "/usr/local/lib/php/vendor/autoload.php";
    require_once "bd.php";

    // Busca los archivos .html.twig dentro de /templates
    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    // Capta la URL limpia
    $ruta_solicitada = isset($_GET['ruta']) ? $_GET['ruta'] : '';

    // Procesamiento la URL (Ejemplo: "noticia/1" se convierte en el array ["noticia", "1"])
    $partes = explode('/', trim($ruta_solicitada, '/'));

    $controlador = $partes[0] !== '' ? $partes[0] : 'portada';   // Si está vacío, carga la portada
    $parametro = isset($partes[1]) ? intval($partes[1]) : 0;     // Extrae el ID si existe

    switch ($controlador) {
        case 'portada':
            require 'controlador_portada.php';
            break;
            
        case 'noticia':
            $id = $parametro; // Pasamos el parámetro extraído a la variable $id que usará el controlador
            require 'controlador_noticia.php';
            break;
            
        case 'imprimir':
            $id = $parametro;
            require 'controlador_imprimir.php';
            break;
            
        default:
            // Si el usuario inventa una URL (ej. localhost/inventado) devuelve un error 404 y detiene la ejecución.
            http_response_code(404);
            die("Error 404: La ruta solicitada no existe en la aplicación.");
    }
?>