<?php
    require_once "modelo_noticias.php";

    // Solo gestores y superusuarios pueden acceder
    if (!isset($_SESSION['usuario_id']) ||
        !in_array($_SESSION['usuario_rol'], ['gestor', 'superusuario'])) {
        header("Location: /");
        exit;
    }

    $accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : 'listar');

    // ── BORRAR NOTICIA ───────────────────────────────────────────────────────
    if ($accion === 'borrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_noticia = isset($_POST['id_noticia']) ? intval($_POST['id_noticia']) : 0;

        if ($id_noticia > 0) {
            borrarNoticia($id_noticia);
        }
        header("Location: /noticias");
        exit;
    }

    // ── CREAR NOTICIA ────────────────────────────────────────────────────────
    if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $titulo      = trim($_POST['titulo']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $tipo        = trim($_POST['tipo']        ?? '');
        $concejalia  = trim($_POST['concejalia']  ?? '');
        $lugar_id    = intval($_POST['lugar_id']  ?? 0);
        $hashtags    = $_POST['hashtags']         ?? [];
        $imgs_sel    = $_POST['imgs_sel']         ?? [];

        if ($titulo !== '' && $descripcion !== '') {
            $nuevo_id = crearNoticia($titulo, $descripcion, $tipo, $concejalia, $lugar_id);
            guardarHashtags($nuevo_id, $hashtags);
            asociarImagenes($nuevo_id, $imgs_sel);
            header("Location: /noticia/" . $nuevo_id);
            exit;
        }
        $accion = 'nueva';
    }

    // ── EDITAR NOTICIA ───────────────────────────────────────────────────────
    if ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_noticia  = intval($_POST['id_noticia']  ?? 0);
        $titulo      = trim($_POST['titulo']        ?? '');
        $descripcion = trim($_POST['descripcion']   ?? '');
        $tipo        = trim($_POST['tipo']          ?? '');
        $concejalia  = trim($_POST['concejalia']    ?? '');
        $lugar_id    = intval($_POST['lugar_id']    ?? 0);
        $hashtags    = $_POST['hashtags']           ?? [];
        $imgs_borrar = $_POST['imgs_borrar']        ?? [];
        $imgs_sel    = $_POST['imgs_sel']           ?? [];

        if ($id_noticia > 0 && $titulo !== '' && $descripcion !== '') {
            actualizarNoticia($id_noticia, $titulo, $descripcion, $tipo, $concejalia, $lugar_id);
            borrarImagenesSeleccionadas($imgs_borrar, $id_noticia);
            asociarImagenes($id_noticia, $imgs_sel);
            borrarHashtagsDeNoticia($id_noticia);
            guardarHashtags($id_noticia, $hashtags);
            header("Location: /noticia/" . $id_noticia);
            exit;
        }
    }

    // ── FORMULARIO NUEVA NOTICIA ─────────────────────────────────────────────
    if ($accion === 'nueva') {
        echo $twig->render('formulario_noticia.html.twig', [
            'noticia'          => null,
            'lugares'          => obtenerLugaresFormulario(),
            'todos_hashtags'   => obtenerTodosHashtags(),
            'tags_noticia'     => [],
            'imagenes'         => [],
            'imgs_disponibles' => listarImagenesDisponibles(0),
            'modo'             => 'crear'
        ]);
        exit;
    }

    // ── FORMULARIO EDITAR NOTICIA ────────────────────────────────────────────
    if ($accion === 'form-editar') {
        $id_noticia = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $noticia    = obtenerNoticiaParaEditar($id_noticia);

        if (!$noticia) { header("Location: /noticias"); exit; }

        echo $twig->render('formulario_noticia.html.twig', [
            'noticia'          => $noticia,
            'lugares'          => obtenerLugaresFormulario(),
            'todos_hashtags'   => obtenerTodosHashtags(),
            'tags_noticia'     => obtenerHashtagsDeNoticia($id_noticia),
            'imagenes'         => obtenerImagenesDeNoticia($id_noticia),
            'imgs_disponibles' => listarImagenesDisponibles($id_noticia),
            'modo'             => 'editar'
        ]);
        exit;
    }

    // ── LISTADO ──────────────────────────────────────────────────────────────
    $busqueda_titulo  = trim($_GET['buscar_titulo']  ?? '');
    $busqueda_desc    = trim($_GET['buscar_desc']    ?? '');
    $busqueda_hashtag = trim($_GET['buscar_hashtag'] ?? '');

    echo $twig->render('gestion_noticias.html.twig', [
        'noticias'         => listarNoticias($busqueda_titulo, $busqueda_desc, $busqueda_hashtag),
        'busqueda_titulo'  => $busqueda_titulo,
        'busqueda_desc'    => $busqueda_desc,
        'busqueda_hashtag' => $busqueda_hashtag,
        'todos_hashtags'   => obtenerTodosHashtags()
    ]);
?>