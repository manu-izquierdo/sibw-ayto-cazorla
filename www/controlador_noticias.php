<?php
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
            // Las imágenes y comentarios se borran en cascada por las FK
            $stmt = $mysqli->prepare("DELETE FROM noticias WHERE id = ?");
            $stmt->bind_param("i", $id_noticia);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: /noticias");
        exit;
    }

    // ── GUARDAR NUEVA NOTICIA ────────────────────────────────────────────────
    if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $titulo      = trim($_POST['titulo']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $tipo        = trim($_POST['tipo']        ?? '');
        $concejalia  = trim($_POST['concejalia']  ?? '');
        $lugar_id    = intval($_POST['lugar_id']  ?? 0);
        $hashtags    = isset($_POST['hashtags'])  ? $_POST['hashtags'] : [];
        $imgs_sel    = isset($_POST['imgs_sel'])  ? $_POST['imgs_sel'] : [];

        if ($titulo !== '' && $descripcion !== '') {

            $stmt = $mysqli->prepare(
                "INSERT INTO noticias (titulo, descripcion, tipo, concejalia, lugar_id)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $lugar_val = $lugar_id > 0 ? $lugar_id : null;
            $stmt->bind_param("ssssi", $titulo, $descripcion, $tipo, $concejalia, $lugar_val);
            $stmt->execute();
            $nuevo_id = $mysqli->insert_id;
            $stmt->close();

            // Asociar hashtags seleccionados
            _guardarHashtags($mysqli, $nuevo_id, $hashtags);

            // Asociar imágenes seleccionadas de la carpeta
            _asociarImagenes($mysqli, $nuevo_id, $imgs_sel);

            header("Location: /noticia/" . $nuevo_id);
            exit;
        }
        $accion = 'nueva';
    }

    // ── GUARDAR EDICIÓN DE NOTICIA ───────────────────────────────────────────
    if ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_noticia  = intval($_POST['id_noticia']   ?? 0);
        $titulo      = trim($_POST['titulo']         ?? '');
        $descripcion = trim($_POST['descripcion']    ?? '');
        $tipo        = trim($_POST['tipo']           ?? '');
        $concejalia  = trim($_POST['concejalia']     ?? '');
        $lugar_id    = intval($_POST['lugar_id']     ?? 0);
        $hashtags    = isset($_POST['hashtags'])     ? $_POST['hashtags'] : [];
        $imgs_borrar = isset($_POST['imgs_borrar'])  ? $_POST['imgs_borrar'] : [];
        $imgs_sel    = isset($_POST['imgs_sel'])     ? $_POST['imgs_sel'] : [];

        if ($id_noticia > 0 && $titulo !== '' && $descripcion !== '') {

            $stmt = $mysqli->prepare(
                "UPDATE noticias SET titulo=?, descripcion=?, tipo=?, concejalia=?, lugar_id=?
                 WHERE id = ?"
            );
            $lugar_val = $lugar_id > 0 ? $lugar_id : null;
            $stmt->bind_param("ssssii", $titulo, $descripcion, $tipo, $concejalia, $lugar_val, $id_noticia);
            $stmt->execute();
            $stmt->close();

            // Borrar imágenes marcadas para eliminar
            foreach ($imgs_borrar as $img_id) {
                $stmt = $mysqli->prepare("DELETE FROM imagenes WHERE id = ? AND noticia_id = ?");
                $img_id_int = intval($img_id);
                $stmt->bind_param("ii", $img_id_int, $id_noticia);
                $stmt->execute();
                $stmt->close();
            }

            // Añadir nuevas imágenes seleccionadas
            _asociarImagenes($mysqli, $id_noticia, $imgs_sel);

            // Actualizar hashtags
            $stmt = $mysqli->prepare("DELETE FROM noticia_hashtag WHERE noticia_id = ?");
            $stmt->bind_param("i", $id_noticia);
            $stmt->execute();
            $stmt->close();
            _guardarHashtags($mysqli, $id_noticia, $hashtags);

            header("Location: /noticia/" . $id_noticia);
            exit;
        }
    }

    // ── FUNCIONES AUXILIARES ─────────────────────────────────────────────────
    function _guardarHashtags($mysqli, $noticia_id, $hashtags) {
        foreach ($hashtags as $nombre_tag) {
            $nombre_tag = trim($nombre_tag);
            if ($nombre_tag === '') continue;

            // Insertar hashtag si no existe (IGNORE evita duplicados)
            $stmt = $mysqli->prepare("INSERT IGNORE INTO hashtags (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombre_tag);
            $stmt->execute();
            $stmt->close();

            // Obtener su id
            $stmt = $mysqli->prepare("SELECT id FROM hashtags WHERE nombre = ?");
            $stmt->bind_param("s", $nombre_tag);
            $stmt->execute();
            $tag = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($tag) {
                $stmt = $mysqli->prepare(
                    "INSERT IGNORE INTO noticia_hashtag (noticia_id, hashtag_id) VALUES (?, ?)"
                );
                $stmt->bind_param("ii", $noticia_id, $tag['id']);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    function _asociarImagenes($mysqli, $noticia_id, $rutas) {
        foreach ($rutas as $ruta) {
            $ruta = trim($ruta);
            if ($ruta === '') continue;

            // Comprobar que esa ruta no está ya asociada a esta noticia
            $stmt = $mysqli->prepare("SELECT id FROM imagenes WHERE noticia_id = ? AND ruta = ?");
            $stmt->bind_param("is", $noticia_id, $ruta);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) { $stmt->close(); continue; }
            $stmt->close();

            $stmt = $mysqli->prepare("INSERT INTO imagenes (noticia_id, ruta) VALUES (?, ?)");
            $stmt->bind_param("is", $noticia_id, $ruta);
            $stmt->execute();
            $stmt->close();
        }
    }

    function _listarImagenesDisponibles($mysqli, $noticia_id) {
        // Leer todos los archivos de img/Noticias/
        $dir = '/var/www/html/img/Noticias/';
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $todas = [];

        if (is_dir($dir)) {
            foreach (scandir($dir) as $archivo) {
                if ($archivo === '.' || $archivo === '..') continue;
                $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                if (in_array($ext, $permitidas)) {
                    $todas[] = 'img/Noticias/' . $archivo;
                }
            }
        }

        // Imágenes ya asociadas a esta noticia
        $ya_asociadas = [];
        if ($noticia_id > 0) {
            $res = $mysqli->query("SELECT ruta FROM imagenes WHERE noticia_id = $noticia_id");
            $ya_asociadas = array_column($res->fetch_all(MYSQLI_ASSOC), 'ruta');
        }

        // Devolvemos solo las que NO están ya asociadas
        return array_values(array_diff($todas, $ya_asociadas));
    }

    // ── MOSTRAR FORMULARIO NUEVA NOTICIA ─────────────────────────────────────
    if ($accion === 'nueva') {
        $lugares  = $mysqli->query("SELECT * FROM lugares ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
        $hashtags = $mysqli->query("SELECT * FROM hashtags ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

        echo $twig->render('formulario_noticia.html.twig', [
            'noticia'            => null,
            'lugares'            => $lugares,
            'todos_hashtags'     => $hashtags,
            'tags_noticia'       => [],
            'imagenes'           => [],
            'imgs_disponibles'   => _listarImagenesDisponibles($mysqli, 0),
            'modo'               => 'crear'
        ]);
        exit;
    }

    // ── MOSTRAR FORMULARIO EDITAR NOTICIA ────────────────────────────────────
    if ($accion === 'form-editar') {
        $id_noticia = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $stmt = $mysqli->prepare("SELECT * FROM noticias WHERE id = ?");
        $stmt->bind_param("i", $id_noticia);
        $stmt->execute();
        $noticia = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$noticia) { header("Location: /noticias"); exit; }

        $lugares  = $mysqli->query("SELECT * FROM lugares ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
        $imagenes = $mysqli->query("SELECT * FROM imagenes WHERE noticia_id = $id_noticia")->fetch_all(MYSQLI_ASSOC);
        $todos_hashtags = $mysqli->query("SELECT * FROM hashtags ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

        $res_tags = $mysqli->query(
            "SELECT h.nombre FROM hashtags h
             JOIN noticia_hashtag nh ON h.id = nh.hashtag_id
             WHERE nh.noticia_id = $id_noticia"
        );
        $tags_noticia = array_column($res_tags->fetch_all(MYSQLI_ASSOC), 'nombre');

        echo $twig->render('formulario_noticia.html.twig', [
            'noticia'            => $noticia,
            'lugares'            => $lugares,
            'todos_hashtags'     => $todos_hashtags,
            'tags_noticia'       => $tags_noticia,
            'imagenes'           => $imagenes,
            'imgs_disponibles'   => _listarImagenesDisponibles($mysqli, $id_noticia),
            'modo'               => 'editar'
        ]);
        exit;
    }

    // ── LISTADO DE NOTICIAS (con búsqueda) ───────────────────────────────────
    $busqueda_titulo   = trim($_GET['buscar_titulo']  ?? '');
    $busqueda_desc     = trim($_GET['buscar_desc']    ?? '');
    $busqueda_hashtag  = trim($_GET['buscar_hashtag'] ?? '');

    $sql = "SELECT n.id, n.titulo, n.fecha, n.tipo, n.concejalia,
                   MIN(i.ruta) as ruta
            FROM noticias n
            LEFT JOIN imagenes i ON n.id = i.noticia_id";

    // Si buscamos por hashtag necesitamos el JOIN con las tablas de hashtags
    if ($busqueda_hashtag !== '') {
        $sql .= " JOIN noticia_hashtag nh ON n.id = nh.noticia_id
                  JOIN hashtags h ON nh.hashtag_id = h.id";
    }

    $condiciones = [];
    $params      = [];
    $tipos        = '';

    if ($busqueda_titulo !== '') {
        $condiciones[] = "n.titulo LIKE ?";
        $params[]      = '%' . $busqueda_titulo . '%';
        $tipos        .= 's';
    }
    if ($busqueda_desc !== '') {
        $condiciones[] = "n.descripcion LIKE ?";
        $params[]      = '%' . $busqueda_desc . '%';
        $tipos        .= 's';
    }
    if ($busqueda_hashtag !== '') {
        $condiciones[] = "h.nombre = ?";
        $params[]      = $busqueda_hashtag;
        $tipos        .= 's';
    }

    if (!empty($condiciones)) {
        $sql .= " WHERE " . implode(" AND ", $condiciones);
    }

    $sql .= " GROUP BY n.id, n.titulo, n.fecha, n.tipo, n.concejalia ORDER BY n.fecha DESC";

    if (!empty($params)) {
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($tipos, ...$params);
        $stmt->execute();
        $noticias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $noticias = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Cargar todos los hashtags para el selector del buscador
    $todos_hashtags = $mysqli->query("SELECT nombre FROM hashtags ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

    echo $twig->render('gestion_noticias.html.twig', [
        'noticias'          => $noticias,
        'busqueda_titulo'   => $busqueda_titulo,
        'busqueda_desc'     => $busqueda_desc,
        'busqueda_hashtag'  => $busqueda_hashtag,
        'todos_hashtags'    => $todos_hashtags
    ]);
?>