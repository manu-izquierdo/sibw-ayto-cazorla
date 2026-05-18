<?php

function obtenerNoticiaParaImprimir($id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $noticia = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    return $noticia;
}

function obtenerImagenesParaImprimir($noticia_id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT ruta FROM imagenes WHERE noticia_id = ?");
    $stmt->bind_param("i", $noticia_id);
    $stmt->execute();
    $imagenes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $imagenes;
}