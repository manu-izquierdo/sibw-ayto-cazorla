<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";

    $host = "lamp-mysql8";
    $db   = "sibw";
    $user = "manu_sibw";
    $pass = "practica3";

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    $sql = "SELECT n.id, n.titulo, MIN(i.ruta) as ruta 
            FROM noticias n 
            LEFT JOIN imagenes i ON n.id = i.noticia_id 
            GROUP BY n.id, n.titulo";

    $resultado = $mysqli->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $mysqli->error);
    }

    $noticias = $resultado->fetch_all(MYSQLI_ASSOC);
    if (!$noticias) {
        die("Error 404: La pagina solicitada no existe.");
    }

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    echo $twig->render('portada.html.twig', [
        'noticias' => $noticias
    ]);
?>