<?php

function listarNoticias($busqueda_titulo = '', $busqueda_desc = '', $busqueda_hashtag = '') {
    $mysqli = conectar();

    $sql = "SELECT n.id, n.titulo, n.fecha, n.tipo, n.concejalia, n.publicado,
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

    // Obtener las rutas de las imágenes antes de borrar en cascada
    $stmt = $mysqli->prepare("SELECT ruta FROM imagenes WHERE noticia_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $rutas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Borrar archivos físicos del disco
    foreach ($rutas as $fila) {
        $ruta_abs = '/var/www/html/' . $fila['ruta'];
        if (file_exists($ruta_abs)) {
            unlink($ruta_abs);
        }
    }

    // Borrar la noticia (imágenes y comentarios se borran en cascada por FK)
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


function borrarImagenesSeleccionadas($imgs_borrar, $noticia_id) {
    foreach ($imgs_borrar as $img_id) {
        $mysqli     = conectar();
        $img_id_int = intval($img_id);

        // Obtener la ruta antes de borrar
        $stmt = $mysqli->prepare("SELECT ruta FROM imagenes WHERE id = ? AND noticia_id = ?");
        $stmt->bind_param("ii", $img_id_int, $noticia_id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Borrar archivo físico
        if ($fila) {
            $ruta_abs = '/var/www/html/' . $fila['ruta'];
            if (file_exists($ruta_abs)) {
                unlink($ruta_abs);
            }
        }

        // Borrar registro de BD
        $stmt = $mysqli->prepare("DELETE FROM imagenes WHERE id = ? AND noticia_id = ?");
        $stmt->bind_param("ii", $img_id_int, $noticia_id);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }
}

function subirImagenes($noticia_id) {
    if (!isset($_FILES['imagenes']) || empty($_FILES['imagenes']['name'][0])) return;

    $dir_abs    = '/var/www/html/img/Noticias/';
    $dir_rel    = 'img/Noticias/';
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!is_dir($dir_abs)) {
        mkdir($dir_abs, 0775, true);
    }

    $mysqli = conectar();

    foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp) {
        if ($_FILES['imagenes']['error'][$i] !== UPLOAD_ERR_OK) continue;

        $ext = strtolower(pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $permitidas)) continue;

        $nombre   = 'n' . $noticia_id . '_' . uniqid() . '.' . $ext;
        $ruta_abs = $dir_abs . $nombre;
        $ruta_rel = $dir_rel . $nombre;

        if (move_uploaded_file($tmp, $ruta_abs)) {
            $stmt = $mysqli->prepare("INSERT INTO imagenes (noticia_id, ruta) VALUES (?, ?)");
            $stmt->bind_param("is", $noticia_id, $ruta_rel);
            $stmt->execute();
            $stmt->close();
        }
    }

    $mysqli->close();
}

function togglePublicado($id_noticia) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "UPDATE noticias SET publicado = 1 - publicado WHERE id = ?"
    );
    $stmt->bind_param('i', $id_noticia);
    $stmt->execute();
    $stmt->close();

    // Devolvemos el nuevo valor para confirmarlo en el cliente
    $stmt2 = $mysqli->prepare("SELECT publicado FROM noticias WHERE id = ?");
    $stmt2->bind_param('i', $id_noticia);
    $stmt2->execute();
    $fila = $stmt2->get_result()->fetch_assoc();
    $stmt2->close();
    $mysqli->close();

    return (int) $fila['publicado'];
}