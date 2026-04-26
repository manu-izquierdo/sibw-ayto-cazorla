<?php
    // Ya NO hay require_once de autoload ni de bd.php por hacerlos dentro del index.php, este "hereda" las variables $mysqli y $twig

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
    
    echo $twig->render('portada.html.twig', [
        'noticias' => $noticias
    ]);
?>