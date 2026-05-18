<?php

function obtenerTodosComentarios() {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "SELECT c.*, n.titulo AS titulo_noticia
         FROM comentarios c
         JOIN noticias n ON c.noticia_id = n.id
         ORDER BY c.fecha DESC"
    );
    $stmt->execute();
    $comentarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $comentarios;
}

function buscarComentarios($busqueda) {
    $mysqli = conectar();
    $like   = '%' . $busqueda . '%';

    $stmt = $mysqli->prepare(
        "SELECT c.*, n.titulo AS titulo_noticia
         FROM comentarios c
         JOIN noticias n ON c.noticia_id = n.id
         WHERE c.texto LIKE ? OR c.nombre LIKE ?
         ORDER BY c.fecha DESC"
    );
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $comentarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $comentarios;
}

function editarComentario($id_comentario, $texto_nuevo) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "UPDATE comentarios SET texto = ?, editado = 1 WHERE id = ?"
    );
    $stmt->bind_param("si", $texto_nuevo, $id_comentario);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}

function borrarComentario($id_comentario) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("DELETE FROM comentarios WHERE id = ?");
    $stmt->bind_param("i", $id_comentario);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}