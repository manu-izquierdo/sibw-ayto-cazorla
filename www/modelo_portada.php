<?php

function obtenerNoticias() {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "SELECT n.id, n.titulo, MIN(i.ruta) as ruta
         FROM noticias n
         LEFT JOIN imagenes i ON n.id = i.noticia_id
         GROUP BY n.id, n.titulo"
    );
    $stmt->execute();
    $noticias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();

    return $noticias;
}