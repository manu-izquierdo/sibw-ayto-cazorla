<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    require_once "bd.php"; // Incluyo la conexión a la base de datos centralizada

    //_________________________________________________________________________________________________________
        // Procesamiento del formulario POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // 1. Recepción y validación básica de existencia
        $noticia_id_post = isset($_POST['noticia_id']) ? intval($_POST['noticia_id']) : 0;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';


        if ($noticia_id_post > 0 && $nombre !== '' && $email !== '' && $texto !== '') {
        
            // --- NUEVO: FILTRO DE LOCALIDADES EN BACKEND ---
            // 1. Obtener la lista de lugares desde la base de datos
            $resLugares = $mysqli->query("SELECT nombre FROM lugares");
            if ($resLugares) {
                $lugaresBD = $resLugares->fetch_all(MYSQLI_ASSOC);
                
                // 2. Aplicar el filtro a la variable $texto
                foreach ($lugaresBD as $lugar) {
                    $pueblo = $lugar['nombre'];
                    // Creamos el patrón: \b (límite de palabra), 'i' (case insensitive)
                    // preg_quote evita que caracteres especiales en el nombre rompan la expresión
                    $patron = '/\b' . preg_quote($pueblo, '/') . '\b/i';
                    $texto = preg_replace($patron, strtoupper($pueblo), $texto);
                }
            }
    
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

    // Validación del parámetro GET
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

    // Consultas de la noticia y sus imagenes
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


    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    echo $twig->render('noticia.html.twig', [
        'noticia' => $noticia,
        'imagenes' => $imagenes,
        'comentarios' => $comentarios,
        'lugares_js' => $listaLugares
    ]);
?>