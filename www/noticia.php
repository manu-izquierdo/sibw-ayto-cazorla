<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);


    $mysqli = new mysqli("lamp-mysql8", "root", "tiger", "sibw");
    
    // 2. Validación del parámetro GET
    $idEvt = isset($_GET['id']) ? (int)$_GET['id'] : 1;

    // 3. Consulta de la noticia
    $res = $mysqli->query("SELECT * FROM noticias WHERE id = $idEvt");
    $noticia = $res->fetch_assoc();

    // 4. Consulta de imágenes relacionadas
    $resImgs = $mysqli->query("SELECT * FROM imagenes WHERE noticia_id = $idEvt");
    $imagenes = $resImgs->fetch_all(MYSQLI_ASSOC);

    echo $twig->render('noticia.html.twig', [
        'noticia' => $noticia,
        'imagenes' => $imagenes
    ]);
?>