<?php
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

        $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();

        // Destruimos la sesión y redirigimos a la portada
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

        // Validaciones básicas
        if ($nombre === '' || $email === '') {
            $error = "El nombre y el correo son obligatorios.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El formato del correo no es válido.";

        } elseif ($password !== '' && strlen($password) < 4) {
            $error = "La contraseña debe tener al menos 4 caracteres.";

        } elseif ($password !== '' && $password !== $password2) {
            $error = "Las contraseñas no coinciden.";

        } else {
            // Comprobar que el email no lo usa otro usuario distinto
            $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $id_usuario);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Ese correo ya está en uso por otra cuenta.";
                $stmt->close();
            } else {
                $stmt->close();

                if ($password !== '') {
                    // Actualizar nombre, email Y contraseña
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $mysqli->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $nombre, $email, $hash, $id_usuario);
                } else {
                    // Actualizar solo nombre y email
                    $stmt = $mysqli->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $nombre, $email, $id_usuario);
                }

                if ($stmt->execute()) {
                    // Actualizamos también los datos de sesión
                    $_SESSION['usuario_nombre'] = $nombre;

                    // Refrescamos los globales de Twig para que el encabezado
                    // muestre el nombre nuevo sin necesidad de hacer logout
                    $twig->addGlobal('sesion_nombre', $nombre);

                    $exito = "Datos actualizados correctamente.";
                } else {
                    $error = "Error al actualizar los datos. Inténtalo de nuevo.";
                }
                $stmt->close();
            }
        }
    }

    // ── CARGAR DATOS ACTUALES DEL USUARIO ────────────────────────────────────
    $stmt = $mysqli->prepare("SELECT nombre, email, rol, fecha_registro FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

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
