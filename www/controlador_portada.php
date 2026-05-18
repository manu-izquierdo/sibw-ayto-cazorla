<?php
    require_once "modelo_portada.php";

    $noticias = obtenerNoticias();

    if (!$noticias) {
        die("Error 404: La página solicitada no existe.");
    }

    echo $twig->render('portada.html.twig', [
        'noticias' => $noticias
    ]);
?>