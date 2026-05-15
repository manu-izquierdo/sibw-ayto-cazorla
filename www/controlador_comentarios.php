<?php
    // Solo moderadores y superusuarios pueden acceder
    if (!isset($_SESSION['usuario_id']) || 
        !in_array($_SESSION['usuario_rol'], ['moderador', 'superusuario'])) {
        header("Location: /");
        exit;
    }

    $error = null;
    $exito = null;

    // ── BORRAR COMENTARIO ────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'borrar') {

        $id_comentario  = isset($_POST['id_comentario'])  ? intval($_POST['id_comentario'])  : 0;
        $origen         = isset($_POST['origen'])         ? $_POST['origen']                 : 'gestion';

        if ($id_comentario > 0) {
            $stmt = $mysqli->prepare("DELETE FROM comentarios WHERE id = ?");
            $stmt->bind_param("i", $id_comentario);
            $stmt->execute();
            $stmt->close();
        }

        // Si venimos desde la página de noticia, volvemos a ella
        if ($origen === 'noticia' && isset($_POST['noticia_id'])) {
            header("Location: /noticia/" . intval($_POST['noticia_id']));
        } else {
            header("Location: /comentarios");
        }
        exit;
    }

    // ── GUARDAR EDICIÓN DE COMENTARIO ────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

        $id_comentario = isset($_POST['id_comentario']) ? intval($_POST['id_comentario']) : 0;
        $texto_nuevo   = isset($_POST['texto'])         ? trim($_POST['texto'])           : '';
        $origen        = isset($_POST['origen'])        ? $_POST['origen']                : 'gestion';

        if ($id_comentario > 0 && $texto_nuevo !== '') {
            $stmt = $mysqli->prepare("UPDATE comentarios SET texto = ?, editado = 1 WHERE id = ?");
            $stmt->bind_param("si", $texto_nuevo, $id_comentario);
            $stmt->execute();
            $stmt->close();
        }

        if ($origen === 'noticia' && isset($_POST['noticia_id'])) {
            header("Location: /noticia/" . intval($_POST['noticia_id']));
        } else {
            header("Location: /comentarios");
        }
        exit;
    }

    // ── BÚSQUEDA Y LISTADO DE COMENTARIOS ────────────────────────────────────
    $busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

    if ($busqueda !== '') {
        $like = '%' . $busqueda . '%';
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
    } else {
        $res = $mysqli->query(
            "SELECT c.*, n.titulo AS titulo_noticia
             FROM comentarios c
             JOIN noticias n ON c.noticia_id = n.id
             ORDER BY c.fecha DESC"
        );
        $comentarios = $res->fetch_all(MYSQLI_ASSOC);
    }

    echo $twig->render('gestion_comentarios.html.twig', [
        'comentarios' => $comentarios,
        'busqueda'    => $busqueda,
        'error'       => $error,
        'exito'       => $exito
    ]);
?>
