<?php

function listarHashtagsConNoticias() {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "SELECT h.id, h.nombre, COUNT(nh.noticia_id) AS total_noticias
         FROM hashtags h
         LEFT JOIN noticia_hashtag nh ON h.id = nh.hashtag_id
         GROUP BY h.id, h.nombre
         ORDER BY total_noticias DESC, h.nombre ASC"
    );
    $stmt->execute();
    $hashtags = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $hashtags;
}

function actualizarHashtag($id, $nombre_nuevo) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("UPDATE hashtags SET nombre = ? WHERE id = ?");
    $stmt->bind_param("si", $nombre_nuevo, $id);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}

function hashtagNombreExiste($nombre, $id_excluir) {
    $mysqli = conectar();
    
    // Comprobar que el nuevo nombre no lo usa otro hashtag
    $stmt = $mysqli->prepare("SELECT id FROM hashtags WHERE nombre = ? AND id != ?");
    $stmt->bind_param("si", $nombre, $id_excluir);
    $stmt->execute();
    $stmt->store_result();
    $existe = $stmt->num_rows > 0;
    $stmt->close();
    $mysqli->close();

    return $existe;
}

function borrarHashtag($id) {
    $mysqli = conectar();
    // Las relaciones en noticia_hashtag se borran en cascada por la FK

    $stmt = $mysqli->prepare("DELETE FROM hashtags WHERE id = ?");
    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}