<?php
    require_once "modelo_noticias.php";

    // Solo gestores y superusuarios pueden acceder
    if (!isset($_SESSION['usuario_id']) ||
        !in_array($_SESSION['usuario_rol'], ['gestor', 'superusuario'])) {
        header("Location: /");
        exit;
    }

    $mysqli = conectar();
    $accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : 'listar');

    // ── BORRAR NOTICIA ───────────────────────────────────────────────────────
    if ($accion === 'borrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_noticia = isset($_POST['id_noticia']) ? intval($_POST['id_noticia']) : 0;

        if ($id_noticia > 0) {
            borrarNoticia($mysqli, $id_noticia);
        }
        $mysqli->close();
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

        if ($titulo !== '' && $descripcion !== '') {
            $nuevo_id = crearNoticia($mysqli, $titulo, $descripcion, $tipo, $concejalia, $lugar_id);
            guardarHashtags($mysqli, $nuevo_id, $hashtags);
            subirImagenes($mysqli, $nuevo_id);
            $mysqli->close();
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

        if ($id_noticia > 0 && $titulo !== '' && $descripcion !== '') {
            actualizarNoticia($mysqli, $id_noticia, $titulo, $descripcion, $tipo, $concejalia, $lugar_id);
            borrarImagenesSeleccionadas($mysqli, $imgs_borrar, $id_noticia);
            subirImagenes($mysqli, $id_noticia);
            borrarHashtagsDeNoticia($mysqli, $id_noticia);
            guardarHashtags($mysqli, $id_noticia, $hashtags);
            $mysqli->close();
            header("Location: /noticia/" . $id_noticia);
            exit;
        }
    }

    // ── FORMULARIO NUEVA NOTICIA ─────────────────────────────────────────────
    if ($accion === 'nueva') {
        echo $twig->render('formulario_noticia.html.twig', [
            'noticia'        => null,
            'lugares'        => obtenerLugaresFormulario($mysqli),
            'todos_hashtags' => obtenerTodosHashtags($mysqli),
            'tags_noticia'   => [],
            'imagenes'       => [],
            'modo'           => 'crear'
        ]);
        $mysqli->close();
        exit;
    }

    // ── FORMULARIO EDITAR NOTICIA ────────────────────────────────────────────
    if ($accion === 'form-editar') {
        $id_noticia = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $noticia    = obtenerNoticiaParaEditar($mysqli, $id_noticia);

        if (!$noticia) {
            $mysqli->close();
            header("Location: /noticias");
            exit;
        }

        echo $twig->render('formulario_noticia.html.twig', [
            'noticia'        => $noticia,
            'lugares'        => obtenerLugaresFormulario($mysqli),
            'todos_hashtags' => obtenerTodosHashtags($mysqli),
            'tags_noticia'   => obtenerHashtagsDeNoticia($mysqli, $id_noticia),
            'imagenes'       => obtenerImagenesDeNoticia($mysqli, $id_noticia),
            'modo'           => 'editar'
        ]);
        $mysqli->close();
        exit;
    }

    // ── LISTADO ──────────────────────────────────────────────────────────────
    $busqueda_titulo  = trim($_GET['buscar_titulo']  ?? '');
    $busqueda_desc    = trim($_GET['buscar_desc']    ?? '');
    $busqueda_hashtag = trim($_GET['buscar_hashtag'] ?? '');

    echo $twig->render('gestion_noticias.html.twig', [
        'noticias'         => listarNoticias($mysqli, $busqueda_titulo, $busqueda_desc, $busqueda_hashtag),
        'busqueda_titulo'  => $busqueda_titulo,
        'busqueda_desc'    => $busqueda_desc,
        'busqueda_hashtag' => $busqueda_hashtag,
        'todos_hashtags'   => obtenerTodosHashtags($mysqli)
    ]);
    $mysqli->close();
?>
