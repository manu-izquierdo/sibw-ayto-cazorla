<?php

function obtenerNoticiaPorId($id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $noticia = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    return $noticia;
}

function obtenerImagenesPorNoticia($noticia_id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT ruta FROM imagenes WHERE noticia_id = ?");
    $stmt->bind_param("i", $noticia_id);
    $stmt->execute();
    $imagenes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $imagenes;
}

function obtenerComentariosPorNoticia($noticia_id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "SELECT * FROM comentarios WHERE noticia_id = ? ORDER BY fecha DESC"
    );
    $stmt->bind_param("i", $noticia_id);
    $stmt->execute();
    $comentarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $comentarios;
}

function obtenerLugares() {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT nombre FROM lugares");
    $stmt->execute();
    $lugares = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return array_column($lugares, 'nombre');
}

function obtenerHashtagsPorNoticia($noticia_id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "SELECT h.nombre FROM hashtags h
         JOIN noticia_hashtag nh ON h.id = nh.hashtag_id
         WHERE nh.noticia_id = ?"
    );
    $stmt->bind_param("i", $noticia_id);
    $stmt->execute();
    $hashtags = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return array_column($hashtags, 'nombre');
}

function insertarComentario($noticia_id, $nombre, $email, $texto) {
    // Filtro de localidades: resalta pueblos en mayúsculas
    $lugares = obtenerLugares();
    foreach ($lugares as $pueblo) {
        $patron = '/\b' . preg_quote($pueblo, '/') . '\b/i';
        $texto  = preg_replace($patron, strtoupper($pueblo), $texto);
    }

    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "INSERT INTO comentarios (noticia_id, nombre, email, texto) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isss", $noticia_id, $nombre, $email, $texto);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}