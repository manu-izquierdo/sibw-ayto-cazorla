<?php
    require_once "modelo_portada.php";

    $mysqli   = conectar();
    $noticias = obtenerNoticias($mysqli);

    if (!$noticias) {
        $mysqli->close();
        die("Error 404: La página solicitada no existe.");
    }

    echo $twig->render('portada.html.twig', [
        'noticias' => $noticias
    ]);
    $mysqli->close();
?>
