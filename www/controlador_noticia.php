<?php
    // Procesamiento del formulario POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Me aseguro que el ID sea un número y quito espacios sobrantes
        $noticia_id_post = isset($_POST['noticia_id']) ? intval($_POST['noticia_id']) : 0;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';

        // Validación del email
        if ($noticia_id_post > 0 && $nombre !== '' && $texto !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
            // Filtro de localidades
            $resLugares = $mysqli->query("SELECT nombre FROM lugares");
            if ($resLugares) {
                $lugaresBD = $resLugares->fetch_all(MYSQLI_ASSOC);
                foreach ($lugaresBD as $lugar) {
                    // Toma los lugares de la BD en la consulta de antes y los compara con el introducido, si coincide, pone el pueblo en mayúscula con preg_replace
                    $pueblo = $lugar['nombre']; 
                    $patron = '/\b' . preg_quote($pueblo, '/') . '\b/i';
                    $texto = preg_replace($patron, strtoupper($pueblo), $texto);
                }
            }

            // Inserción segura con prepare (evita inyecciones de SQL)
            $stmt = $mysqli->prepare("INSERT INTO comentarios (noticia_id, nombre, email, texto) VALUES (?, ?, ?, ?)");
            
            if ($stmt) {
                $stmt->bind_param("isss", $noticia_id_post, $nombre, $email, $texto); // "isss" es una cadena que le dice a PHP qué tipo de datos vas a meter en los huecos (?) de la consulta
                $stmt->execute();
                $stmt->close();

                // si el usuario refresca el navegador, no se envíe el comentario dos veces.
                header("Location: /noticia/" . $noticia_id_post);
                exit;
            } else {
                die("Error en la preparación de la consulta de inserción.");
            }
        }
    }

    // La variable $id ya existe, index.php se la inyecta antes de cargar este archivo
    $sql_noticia = "SELECT * FROM noticias WHERE id = $id";
    $sql_imagenes = "SELECT ruta FROM imagenes WHERE noticia_id = $id";
    $sql_comments = "SELECT * FROM comentarios WHERE noticia_id = $id ORDER BY fecha DESC";
    $sql_lugar = "SELECT nombre FROM lugares";

    $res = $mysqli->query($sql_noticia);
    $noticia = $res->fetch_assoc();
    if (!$noticia) {
        die("Error 404: La noticia solicitada no existe.");
    }

    $resImgs = $mysqli->query($sql_imagenes);
    $imagenes = $resImgs->fetch_all(MYSQLI_ASSOC);

    $resComentarios = $mysqli->query($sql_comments);
    $comentarios = $resComentarios->fetch_all(MYSQLI_ASSOC);

    $resLugaresFiltro = $mysqli->query($sql_lugar);
    $lugaresBD = $resLugaresFiltro->fetch_all(MYSQLI_ASSOC);
    
    $listaLugares = [];
    foreach ($lugaresBD as $lugar) {
        $listaLugares[] = $lugar['nombre'];
    }

    // Obtener hashtags de la noticia
    $resHashtags  = $mysqli->query(
        "SELECT h.nombre FROM hashtags h
         JOIN noticia_hashtag nh ON h.id = nh.hashtag_id
         WHERE nh.noticia_id = $id"
    );
    $hashtags = array_column($resHashtags->fetch_all(MYSQLI_ASSOC), 'nombre');
    
    echo $twig->render('noticia.html.twig', [
        'noticia' => $noticia,
        'imagenes' => $imagenes,
        'comentarios' => $comentarios,
        'lugares_js' => $listaLugares,
        'hashtags'   => $hashtags
    ]);
?>