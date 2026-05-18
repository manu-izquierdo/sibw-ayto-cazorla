<?php
    require_once "modelo_noticia.php";

    // ── PROCESAR NUEVO COMENTARIO ─────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $noticia_id_post = isset($_POST['noticia_id']) ? intval($_POST['noticia_id']) : 0;
        $nombre          = isset($_POST['nombre'])     ? trim($_POST['nombre'])       : '';
        $email           = isset($_POST['email'])      ? trim($_POST['email'])        : '';
        $texto           = isset($_POST['texto'])      ? trim($_POST['texto'])        : '';

        if ($noticia_id_post > 0 && $nombre !== '' && $texto !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            insertarComentario($noticia_id_post, $nombre, $email, $texto);
            header("Location: /noticia/" . $noticia_id_post);
            exit;
        }
    }

    // ── CARGAR DATOS DE LA NOTICIA ────────────────────────────────────────────
    $noticia = obtenerNoticiaPorId($id);

    if (!$noticia) {
        die("Error 404: La noticia solicitada no existe.");
    }

    $imagenes    = obtenerImagenesPorNoticia($id);
    $comentarios = obtenerComentariosPorNoticia($id);
    $lugares_js  = obtenerLugares();
    $hashtags    = obtenerHashtagsPorNoticia($id);

    echo $twig->render('noticia.html.twig', [
        'noticia'     => $noticia,
        'imagenes'    => $imagenes,
        'comentarios' => $comentarios,
        'lugares_js'  => $lugares_js,
        'hashtags'    => $hashtags
    ]);
?>