<?php
    require_once "modelo_comentarios.php";

    // Solo moderadores y superusuarios pueden acceder
    if (!isset($_SESSION['usuario_id']) || 
        !in_array($_SESSION['usuario_rol'], ['moderador', 'superusuario'])) {
        header("Location: /");
        exit;
    }

    $error = null;
    $exito = null;

    // ── BORRAR COMENTARIO ────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'borrar') {

        $id_comentario  = isset($_POST['id_comentario'])  ? intval($_POST['id_comentario'])  : 0;
        $origen         = isset($_POST['origen'])         ? $_POST['origen']                 : 'gestion';

        if ($id_comentario > 0) {
            borrarComentario($id_comentario);
        }

        // Si venimos desde la página de noticia, volvemos a ella
        if ($origen === 'noticia' && isset($_POST['noticia_id'])) {
            header("Location: /noticia/" . intval($_POST['noticia_id']));
        } else {
            header("Location: /comentarios");
        }
        exit;
    }

    // ── EDITAR COMENTARIO ────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

        $id_comentario = isset($_POST['id_comentario']) ? intval($_POST['id_comentario']) : 0;
        $texto_nuevo   = isset($_POST['texto'])         ? trim($_POST['texto'])           : '';
        $origen        = isset($_POST['origen'])        ? $_POST['origen']                : 'gestion';

        if ($id_comentario > 0 && $texto_nuevo !== '') {
            editarComentario($id_comentario, $texto_nuevo);
        }

        if ($origen === 'noticia' && isset($_POST['noticia_id'])) {
            header("Location: /noticia/" . intval($_POST['noticia_id']));
        } else {
            header("Location: /comentarios");
        }
        exit;
    }

    // ── LISTADO Y BÚSQUEDA ───────────────────────────────────────────────────
    $busqueda    = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
    $comentarios = $busqueda !== ''
        ? buscarComentarios($busqueda)
        : obtenerTodosComentarios();

    echo $twig->render('gestion_comentarios.html.twig', [
        'comentarios' => $comentarios,
        'busqueda'    => $busqueda,
        'error'       => $error,
        'exito'       => $exito
    ]);
?>
