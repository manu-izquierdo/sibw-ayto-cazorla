<?php
    require_once "modelo_registro.php";

    // Si ya está logueado, redirigir a portada
    if (isset($_SESSION['usuario_id'])) {
        header("Location: /");
        exit;
    }

    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nombre    = isset($_POST['nombre'])    ? trim($_POST['nombre'])    : '';
        $email     = isset($_POST['email'])     ? trim($_POST['email'])     : '';
        $password  = isset($_POST['password'])  ? trim($_POST['password'])  : '';
        $password2 = isset($_POST['password2']) ? trim($_POST['password2']) : '';

        // Validaciones
        if ($nombre === '' || $email === '' || $password === '' || $password2 === '') {
            $error = "Por favor, rellena todos los campos.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El formato del correo no es válido.";

        } elseif (strlen($password) < 4) {
            $error = "La contraseña debe tener al menos 4 caracteres.";

        } elseif ($password !== $password2) {
            $error = "Las contraseñas no coinciden.";

        } elseif (emailExiste($email)) {
            $error = "Ese correo electrónico ya está registrado.";

        } else {
            $hash     = password_hash($password, PASSWORD_DEFAULT);
            $nuevo_id = insertarUsuario($nombre, $email, $hash);

            if ($nuevo_id) {
                $_SESSION['usuario_id']     = $nuevo_id;
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_rol']    = 'registrado';

                header("Location: /");
                exit;
            } else {
                $error = "Error al registrar el usuario. Inténtalo de nuevo.";
            }
        }
    }

    echo $twig->render('registro.html.twig', [
        'error' => $error
    ]);
?>