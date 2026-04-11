<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";

    $host = "lamp-mysql8";
    $db   = "sibw";
    $user = "manu_sibw";
    $pass = "practica3";

    $mysqli = new mysqli($host, $user, $pass, $db);

    //_________________________________________________________________________________________________________
        // Procesamiento del formulario POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. Recepción y validación básica de existencia
        $noticia_id_post = isset($_POST['noticia_id']) ? intval($_POST['noticia_id']) : 0;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';

        // 2. Ejecución solo si hay datos válidos
        if ($noticia_id_post > 0 && !empty($nombre) && !empty($email) && !empty($texto)) {
            
            // 3. Sentencia Preparada: Se usan '?' como marcadores de posición
            $stmt = $mysqli->prepare("INSERT INTO comentarios (noticia_id, nombre, email, texto) VALUES (?, ?, ?, ?)");
            
            if ($stmt) {
                // 4. Vinculación de parámetros (Bind)
                // 'isss' indica los tipos de datos: i (integer), s (string), s (string), s (string)
                $stmt->bind_param("isss", $noticia_id_post, $nombre, $email, $texto);
                $stmt->execute();
                $stmt->close();

                // 5. Patrón PRG (Post/Redirect/Get)
                // Se redirige a la misma página por GET para evitar que al recargar el navegador 
                // se vuelva a enviar el formulario duplicando el comentario.
                header("Location: noticia.php?id=" . $noticia_id_post);
                exit;
            } else {
                die("Error en la preparación de la consulta de inserción.");
            }
        }
    }
    //_________________________________________________________________________________________________________


    if ($mysqli->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    // Validación del parámetro GET
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

    // Consultas de la noticia y sus imagenes
    $sql_noticia = "SELECT * FROM noticias WHERE id = $id";
    $sql_imagenes = "SELECT ruta FROM imagenes WHERE noticia_id = $id";
    $sql_comments = "SELECT * FROM comentarios WHERE noticia_id = $id ORDER BY fecha DESC";

    $res = $mysqli->query($sql_noticia);
    $noticia = $res->fetch_assoc();
    if (!$noticia) {
        die("Error 404: La noticia solicitada no existe.");
    }

    $resImgs = $mysqli->query($sql_imagenes);
    $imagenes = $resImgs->fetch_all(MYSQLI_ASSOC);

    $resComentarios = $mysqli->query($sql_comments);
    $comentarios = $resComentarios->fetch_all(MYSQLI_ASSOC);

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    echo $twig->render('noticia.html.twig', [
        'noticia' => $noticia,
        'imagenes' => $imagenes,
        'comentarios' => $comentarios
    ]);
?>