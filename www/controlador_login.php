<?php
    require_once "modelo_login.php";

    // Si ya está logueado, redirigir a portada
    if (isset($_SESSION['usuario_id'])) {
        header("Location: /");
        exit;
    }

    $mysqli = conectar();
    $error  = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        if ($email === '' || $password === '') {
            $error = "Por favor, rellena todos los campos.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El formato del correo no es válido.";
        } else {
            $usuario = obtenerUsuarioPorEmail($mysqli, $email);

            if ($usuario && password_verify($password, $usuario['password'])) {
                // Credenciales correctas: guardamos datos en sesión
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol']    = $usuario['rol'];

                $mysqli->close();
                header("Location: /");
                exit;
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        }
    }

    echo $twig->render('login.html.twig', [
        'error' => $error
    ]);
    $mysqli->close();
?>
