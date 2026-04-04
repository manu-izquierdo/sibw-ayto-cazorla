<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    // 3. (Futuro) Aquí harás las consultas SQL a la base de datos

    echo $twig->render('noticia_imprimir.html.twig', []);
?>