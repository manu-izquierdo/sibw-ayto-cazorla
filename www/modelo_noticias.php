<?php

function listarNoticias($busqueda_titulo = '', $busqueda_desc = '', $busqueda_hashtag = '') {
    $mysqli = conectar();

    $sql = "SELECT n.id, n.titulo, n.fecha, n.tipo, n.concejalia,
                   MIN(i.ruta) as ruta
            FROM noticias n
            LEFT JOIN imagenes i ON n.id = i.noticia_id";

    if ($busqueda_hashtag !== '') {
        $sql .= " JOIN noticia_hashtag nh ON n.id = nh.noticia_id
                  JOIN hashtags h ON nh.hashtag_id = h.id";
    }

    $condiciones = [];
    $params      = [];
    $tipos       = '';

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

    $mysqli->close();
    return $noticias;
}

function obtenerNoticiaParaEditar($id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $noticia = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    return $noticia;
}

function obtenerLugaresFormulario() {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT * FROM lugares ORDER BY nombre");
    $stmt->execute();
    $lugares = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $lugares;
}

function obtenerTodosHashtags() {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT * FROM hashtags ORDER BY nombre");
    $stmt->execute();
    $hashtags = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $hashtags;
}

function obtenerHashtagsDeNoticia($noticia_id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "SELECT h.nombre FROM hashtags h
         JOIN noticia_hashtag nh ON h.id = nh.hashtag_id
         WHERE nh.noticia_id = ?"
    );
    $stmt->bind_param("i", $noticia_id);
    $stmt->execute();
    $tags = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return array_column($tags, 'nombre');
}

function obtenerImagenesDeNoticia($noticia_id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT * FROM imagenes WHERE noticia_id = ?");
    $stmt->bind_param("i", $noticia_id);
    $stmt->execute();
    $imagenes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $imagenes;
}

function listarImagenesDisponibles($noticia_id) {
    $dir       = '/var/www/html/img/Noticias/';
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $todas     = [];

    if (is_dir($dir)) {
        foreach (scandir($dir) as $archivo) {
            if ($archivo === '.' || $archivo === '..') continue;
            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
            if (in_array($ext, $permitidas)) {
                $todas[] = 'img/Noticias/' . $archivo;
            }
        }
    }

    $mysqli       = conectar();
    $ya_asociadas = [];

    if ($noticia_id > 0) {
        $stmt = $mysqli->prepare("SELECT ruta FROM imagenes WHERE noticia_id = ?");
        $stmt->bind_param("i", $noticia_id);
        $stmt->execute();
        $res          = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $ya_asociadas = array_column($res, 'ruta');
        $stmt->close();
    }

    $mysqli->close();
    return array_values(array_diff($todas, $ya_asociadas));
}

function crearNoticia($titulo, $descripcion, $tipo, $concejalia, $lugar_id) {
    $mysqli    = conectar();
    $lugar_val = $lugar_id > 0 ? $lugar_id : null;

    $stmt = $mysqli->prepare(
        "INSERT INTO noticias (titulo, descripcion, tipo, concejalia, lugar_id)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssssi", $titulo, $descripcion, $tipo, $concejalia, $lugar_val);
    $stmt->execute();
    $nuevo_id = $mysqli->insert_id;
    $stmt->close();
    $mysqli->close();

    return $nuevo_id;
}

function actualizarNoticia($id, $titulo, $descripcion, $tipo, $concejalia, $lugar_id) {
    $mysqli    = conectar();
    $lugar_val = $lugar_id > 0 ? $lugar_id : null;

    $stmt = $mysqli->prepare(
        "UPDATE noticias SET titulo=?, descripcion=?, tipo=?, concejalia=?, lugar_id=?
         WHERE id = ?"
    );
    $stmt->bind_param("ssssii", $titulo, $descripcion, $tipo, $concejalia, $lugar_val, $id);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}

function borrarNoticia($id) {
    $mysqli = conectar();
    // Las imágenes y comentarios se borran en cascada por las FK

    $stmt = $mysqli->prepare("DELETE FROM noticias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}

function guardarHashtags($noticia_id, $hashtags) {
    foreach ($hashtags as $nombre_tag) {
        $nombre_tag = trim($nombre_tag);
        if ($nombre_tag === '') continue;

        $mysqli = conectar();

        $stmt = $mysqli->prepare("INSERT IGNORE INTO hashtags (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre_tag);
        $stmt->execute();
        $stmt->close();

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

        $mysqli->close();
    }
}

function borrarHashtagsDeNoticia($noticia_id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("DELETE FROM noticia_hashtag WHERE noticia_id = ?");
    $stmt->bind_param("i", $noticia_id);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();
}

function asociarImagenes($noticia_id, $rutas) {
    foreach ($rutas as $ruta) {
        $ruta = trim($ruta);
        if ($ruta === '') continue;

        $mysqli = conectar();

        $stmt = $mysqli->prepare("SELECT id FROM imagenes WHERE noticia_id = ? AND ruta = ?");
        $stmt->bind_param("is", $noticia_id, $ruta);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $stmt->close();
            $stmt = $mysqli->prepare("INSERT INTO imagenes (noticia_id, ruta) VALUES (?, ?)");
            $stmt->bind_param("is", $noticia_id, $ruta);
            $stmt->execute();
        }

        $stmt->close();
        $mysqli->close();
    }
}

function borrarImagenesSeleccionadas($imgs_borrar, $noticia_id) {
    foreach ($imgs_borrar as $img_id) {
        $mysqli     = conectar();
        $img_id_int = intval($img_id);

        $stmt = $mysqli->prepare("DELETE FROM imagenes WHERE id = ? AND noticia_id = ?");
        $stmt->bind_param("ii", $img_id_int, $noticia_id);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }
}