<?php
    // Carga Twig y la conexión a la Base de Datos
    require_once "/usr/local/lib/php/vendor/autoload.php";
    require_once "bd.php";

    // Busca los archivos .html.twig dentro de /templates
    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    // Añadimos los datos de sesión como variables globales de Twig para que todas las plantillas puedan acceder a ellos
    $twig->addGlobal('sesion_nombre', $_SESSION['usuario_nombre'] ?? null);
    $twig->addGlobal('sesion_rol',    $_SESSION['usuario_rol']    ?? null);
    $twig->addGlobal('sesion_id',     $_SESSION['usuario_id']     ?? null);

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

        case 'login':
            require 'controlador_login.php';
            break;

        case 'registro':
            require 'controlador_registro.php';
            break;

        case 'logout':
            require 'controlador_logout.php';
            break;

        case 'perfil':
            require 'controlador_perfil.php';
            break;

        case 'comentarios':
            require 'controlador_comentarios.php';
            break;

        case 'noticias':
            require 'controlador_noticias.php';
            break;

        case 'usuarios':
            require 'controlador_usuarios.php';
            break;
        
        default:
            http_response_code(404);
            die("Error 404: La ruta solicitada no existe en la aplicación.");
    }
?>