<?php
    // Si ya está logueado, redirigir a portada
    if (isset($_SESSION['usuario_id'])) {
        header("Location: /");
        exit;
    }

    $error = null;
    $exito = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nombre   = isset($_POST['nombre'])    ? trim($_POST['nombre'])    : '';
        $email    = isset($_POST['email'])     ? trim($_POST['email'])     : '';
        $password = isset($_POST['password'])  ? trim($_POST['password'])  : '';
        $password2= isset($_POST['password2']) ? trim($_POST['password2']) : '';

        // Validaciones
        if ($nombre === '' || $email === '' || $password === '' || $password2 === '') {
            $error = "Por favor, rellena todos los campos.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El formato del correo no es válido.";

        } elseif (strlen($password) < 4) {
            $error = "La contraseña debe tener al menos 4 caracteres.";

        } elseif ($password !== $password2) {
            $error = "Las contraseñas no coinciden.";

        } else {
            // Comprobar si el email ya está registrado
            $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Ese correo electrónico ya está registrado.";
                $stmt->close();
            } else {
                $stmt->close();

                // Hashear la contraseña antes de guardarla
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Insertar nuevo usuario con rol 'registrado' por defecto
                $stmt = $mysqli->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'registrado')");
                $stmt->bind_param("sss", $nombre, $email, $hash);

                if ($stmt->execute()) {
                    // Logueamos automáticamente al usuario recién registrado
                    $_SESSION['usuario_id']     = $mysqli->insert_id;
                    $_SESSION['usuario_nombre'] = $nombre;
                    $_SESSION['usuario_rol']    = 'registrado';
                    $stmt->close();

                    header("Location: /");
                    exit;
                } else {
                    $error = "Error al registrar el usuario. Inténtalo de nuevo.";
                    $stmt->close();
                }
            }
        }
    }

    echo $twig->render('registro.html.twig', [
        'error' => $error
    ]);
?>
