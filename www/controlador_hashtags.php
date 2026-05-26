<?php
    require_once "modelo_hashtags.php";

    // Solo gestores y superusuarios pueden acceder
    if (!isset($_SESSION['usuario_id']) ||
        !in_array($_SESSION['usuario_rol'], ['gestor', 'superusuario'])) {
        header("Location: /");
        exit;
    }

    $mysqli = conectar();
    $error  = null;
    $exito  = null;

    // ── BORRAR HASHTAG ───────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'borrar') {

        $id_tag = intval($_POST['id_hashtag'] ?? 0);

        if ($id_tag > 0) {
            borrarHashtag($mysqli, $id_tag);
            $exito = "Hashtag eliminado correctamente.";
        }
    }

    // ── EDITAR HASHTAG ───────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

        $id_tag       = intval($_POST['id_hashtag'] ?? 0);
        $nombre_nuevo = trim(strtolower($_POST['nombre'] ?? ''));

        if ($id_tag > 0 && $nombre_nuevo !== '') {
            if (hashtagNombreExiste($mysqli, $nombre_nuevo, $id_tag)) {
                $error = "Ya existe un hashtag con ese nombre.";
            } else {
                actualizarHashtag($mysqli, $id_tag, $nombre_nuevo);
                $exito = "Hashtag actualizado correctamente.";
            }
        }
    }

    echo $twig->render('gestion_hashtags.html.twig', [
        'hashtags' => listarHashtagsConNoticias($mysqli),
        'error'    => $error,
        'exito'    => $exito
    ]);
    $mysqli->close();
?>
