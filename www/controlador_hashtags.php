<?php
    // Solo gestores y superusuarios pueden acceder
    if (!isset($_SESSION['usuario_id']) ||
        !in_array($_SESSION['usuario_rol'], ['gestor', 'superusuario'])) {
        header("Location: /");
        exit;
    }

    $error = null;
    $exito = null;

    // ── BORRAR HASHTAG ───────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'borrar') {

        $id_tag = intval($_POST['id_hashtag'] ?? 0);

        if ($id_tag > 0) {
            // Las relaciones en noticia_hashtag se borran en cascada por la FK
            $stmt = $mysqli->prepare("DELETE FROM hashtags WHERE id = ?");
            $stmt->bind_param("i", $id_tag);
            $stmt->execute();
            $stmt->close();
            $exito = "Hashtag eliminado correctamente.";
        }
    }

    // ── EDITAR HASHTAG ───────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

        $id_tag      = intval($_POST['id_hashtag'] ?? 0);
        $nombre_nuevo = trim(strtolower($_POST['nombre'] ?? ''));

        if ($id_tag > 0 && $nombre_nuevo !== '') {

            // Comprobar que el nuevo nombre no lo usa otro hashtag
            $stmt = $mysqli->prepare("SELECT id FROM hashtags WHERE nombre = ? AND id != ?");
            $stmt->bind_param("si", $nombre_nuevo, $id_tag);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Ya existe un hashtag con ese nombre.";
                $stmt->close();
            } else {
                $stmt->close();
                $stmt = $mysqli->prepare("UPDATE hashtags SET nombre = ? WHERE id = ?");
                $stmt->bind_param("si", $nombre_nuevo, $id_tag);
                $stmt->execute();
                $stmt->close();
                $exito = "Hashtag actualizado correctamente.";
            }
        }
    }

    // ── LISTAR HASHTAGS CON NÚMERO DE NOTICIAS ASOCIADAS ────────────────────
    $hashtags = $mysqli->query(
        "SELECT h.id, h.nombre, COUNT(nh.noticia_id) AS total_noticias
         FROM hashtags h
         LEFT JOIN noticia_hashtag nh ON h.id = nh.hashtag_id
         GROUP BY h.id, h.nombre
         ORDER BY total_noticias DESC, h.nombre ASC"
    )->fetch_all(MYSQLI_ASSOC);

    echo $twig->render('gestion_hashtags.html.twig', [
        'hashtags' => $hashtags,
        'error'    => $error,
        'exito'    => $exito
    ]);
?>
