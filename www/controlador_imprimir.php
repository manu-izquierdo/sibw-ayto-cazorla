<?php

    // Consultas para obtener todas las noticias de un id especifico y la ruta a sus imágenes
    $sql_noticia = "SELECT * FROM noticias WHERE id = $id";
    $sql_imagenes = "SELECT ruta FROM imagenes WHERE noticia_id = $id";

    // Lanza la consulta a la base de datos y almacena en $res lo que devuelva
    $res = $mysqli->query($sql_noticia);
    // Extrae el resultado como un array asociativo
    $noticia = $res->fetch_assoc();

    $resImgs = $mysqli->query($sql_imagenes);
    // Extrae todas las filas encontradas, ya que una noticia puede tener varias fotos
    $imagenes = $resImgs->fetch_all(MYSQLI_ASSOC);

    // Envía los datos obtenidos al archivo plantilla
    echo $twig->render('noticia_imprimir.html.twig', [
        'noticia' => $noticia,
        'imagenes' => $imagenes
    ]);
?>
