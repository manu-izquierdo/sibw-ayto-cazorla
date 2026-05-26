<?php
require_once "modelo_portada.php";
require_once "modelo_noticias.php";

header('Content-Type: application/json; charset=utf-8');

$mysqli = conectar();

// ── Búsqueda ──────────────────────────────────────────────────────────────────
if ($subaccion === 'buscar') {

    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

    if ($tipo === 'portada') {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        echo json_encode($q !== '' ? buscarNoticiasPublicadas($mysqli, $q) : []);

    } elseif ($tipo === 'gestion') {
        if (!isset($_SESSION['usuario_id']) ||
            !in_array($_SESSION['usuario_rol'], ['gestor', 'superusuario'])) {
            $mysqli->close();
            http_response_code(403);
            echo json_encode([]);
            exit;
        }
        $titulo  = trim($_GET['buscar_titulo']  ?? '');
        $desc    = trim($_GET['buscar_desc']    ?? '');
        $hashtag = trim($_GET['buscar_hashtag'] ?? '');
        echo json_encode(listarNoticias($mysqli, $titulo, $desc, $hashtag));

    } else {
        echo json_encode([]);
    }

// ── Toggle publicado ──────────────────────────────────────────────────────────
} elseif ($subaccion === 'toggle-publicado') {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
        !isset($_SESSION['usuario_id']) ||
        !in_array($_SESSION['usuario_rol'], ['gestor', 'superusuario'])) {
        $mysqli->close();
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    $id = intval($_POST['id_noticia'] ?? 0);
    echo $id > 0
        ? json_encode(['publicado' => togglePublicado($mysqli, $id)])
        : json_encode(['error' => 'ID inválido']);

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Acción no encontrada']);
}

$mysqli->close();
