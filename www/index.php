<?php
require_once "/usr/local/lib/php/vendor/autoload.php";
require_once "bd.php";

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// 2. Recepción de la URL limpia interceptada por Apache
$ruta_solicitada = isset($_GET['ruta']) ? $_GET['ruta'] : '';

// 3. Procesamiento de la URL (Ejemplo: "noticia/1" se convierte en el array ["noticia", "1"])
$partes = explode('/', trim($ruta_solicitada, '/'));

// 4. Asignación de variables de enrutamiento
$controlador = $partes[0] !== '' ? $partes[0] : 'portada'; // Si está vacío, carga la portada
$parametro = isset($partes[1]) ? intval($partes[1]) : 0;     // Extrae el ID si existe

// 5. El Enrutador (Router)
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
        // Si el usuario inventa una URL (ej. localhost/inventado)
        http_response_code(404);
        die("Error 404: La ruta solicitada no existe en la aplicación.");
}
?>