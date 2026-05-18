<?php
    require_once "modelo_imprimir.php";

    $noticia  = obtenerNoticiaParaImprimir($id);
    $imagenes = obtenerImagenesParaImprimir($id);

    echo $twig->render('noticia_imprimir.html.twig', [
        'noticia'  => $noticia,
        'imagenes' => $imagenes
    ]);
?>