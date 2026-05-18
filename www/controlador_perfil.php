<?php
    require_once "modelo_perfil.php";

    // Solo usuarios logueados pueden acceder
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: /login");
        exit;
    }

    $error = null;
    $exito = null;
    $id_usuario = $_SESSION['usuario_id'];

    // ── ELIMINAR CUENTA ──────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {

        eliminarUsuario($id_usuario);
        $_SESSION = [];
        session_destroy();
        header("Location: /");
        exit;
    }

    // ── EDITAR DATOS ─────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

        $nombre    = isset($_POST['nombre'])    ? trim($_POST['nombre'])    : '';
        $email     = isset($_POST['email'])     ? trim($_POST['email'])     : '';
        $password  = isset($_POST['password'])  ? trim($_POST['password'])  : '';
        $password2 = isset($_POST['password2']) ? trim($_POST['password2']) : '';

        if ($nombre === '' || $email === '') {
            $error = "El nombre y el correo son obligatorios.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El formato del correo no es válido.";

        } elseif ($password !== '' && strlen($password) < 4) {
            $error = "La contraseña debe tener al menos 4 caracteres.";

        } elseif ($password !== '' && $password !== $password2) {
            $error = "Las contraseñas no coinciden.";

        } elseif (emailEnUso($email, $id_usuario)) {
            $error = "Ese correo ya está en uso por otra cuenta.";

        } else {
            if ($password !== '') {
                $hash      = password_hash($password, PASSWORD_DEFAULT);
                $resultado = actualizarUsuarioConPassword($id_usuario, $nombre, $email, $hash);
            } else {
                $resultado = actualizarUsuario($id_usuario, $nombre, $email);
            }

            if ($resultado) {
                $_SESSION['usuario_nombre'] = $nombre;
                $twig->addGlobal('sesion_nombre', $nombre);
                $exito = "Datos actualizados correctamente.";
            } else {
                $error = "Error al actualizar los datos. Inténtalo de nuevo.";
            }
        }
    }

    // ── CARGAR DATOS ACTUALES ────────────────────────────────────────────────
    $usuario = obtenerUsuarioPorId($id_usuario);

    if (!$usuario) {
        // El usuario fue eliminado o no existe, cerramos sesión
        $_SESSION = [];
        session_destroy();
        header("Location: /");
        exit;
    }

    echo $twig->render('perfil.html.twig', [
        'usuario' => $usuario,
        'error'   => $error,
        'exito'   => $exito
    ]);
?>
