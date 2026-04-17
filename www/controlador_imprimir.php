<?php

    // Consultas de la noticia y sus imagenes
    $sql_noticia = "SELECT * FROM noticias WHERE id = $id";
    $sql_imagenes = "SELECT ruta FROM imagenes WHERE noticia_id = $id";

    $res = $mysqli->query($sql_noticia);
    $noticia = $res->fetch_assoc();

    $resImgs = $mysqli->query($sql_imagenes);
    $imagenes = $resImgs->fetch_all(MYSQLI_ASSOC);


    echo $twig->render('noticia_imprimir.html.twig', [
        'noticia' => $noticia,
        'imagenes' => $imagenes
    ]);
?>
