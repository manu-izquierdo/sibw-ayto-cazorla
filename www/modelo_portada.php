<?php

function obtenerNoticias($mysqli) {
    $stmt = $mysqli->prepare(
        "SELECT n.id, n.titulo, MIN(i.ruta) as ruta
         FROM noticias n
         LEFT JOIN imagenes i ON n.id = i.noticia_id
         WHERE n.publicado = 1
         GROUP BY n.id, n.titulo
         ORDER BY n.fecha DESC"
    );
    $stmt->execute();
    $noticias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $noticias;
}

// Búsqueda AJAX para el desplegable de portada
function buscarNoticiasPublicadas($mysqli, $q) {
    $like = '%' . $q . '%';

    $stmt = $mysqli->prepare(
        "SELECT id, titulo
         FROM noticias
         WHERE titulo LIKE ? AND publicado = 1
         ORDER BY fecha DESC
         LIMIT 8"
    );
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $resultados;
}
