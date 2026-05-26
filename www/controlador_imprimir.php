<?php
    require_once "modelo_imprimir.php";

    $mysqli   = conectar();
    $noticia  = obtenerNoticiaParaImprimir($mysqli, $id);
    $imagenes = obtenerImagenesParaImprimir($mysqli, $id);

    echo $twig->render('noticia_imprimir.html.twig', [
        'noticia'  => $noticia,
        'imagenes' => $imagenes
    ]);
    $mysqli->close();
?>
