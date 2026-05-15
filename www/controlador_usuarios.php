<?php
    // Solo el superusuario puede acceder
    if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'superusuario') {
        header("Location: /");
        exit;
    }

    $error = null;
    $exito = null;

    // ── ELIMINAR USUARIO ─────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {

        $id_objetivo = intval($_POST['id_usuario'] ?? 0);

        // No puede eliminarse a sí mismo
        if ($id_objetivo === intval($_SESSION['usuario_id'])) {
            $error = "No puedes eliminar tu propia cuenta desde aquí.";

        } else {
            // Comprobar que no es el único superusuario
            $stmt = $mysqli->prepare("SELECT rol FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $id_objetivo);
            $stmt->execute();
            $objetivo = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($objetivo && $objetivo['rol'] === 'superusuario') {
                $res   = $mysqli->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'superusuario'");
                $total = $res->fetch_assoc()['total'];

                if ($total <= 1) {
                    $error = "No puedes eliminar al único superusuario del sistema.";
                }
            }

            if (!$error) {
                $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->bind_param("i", $id_objetivo);
                $stmt->execute();
                $stmt->close();
                $exito = "Usuario eliminado correctamente.";
            }
        }
    }

    // ── CAMBIAR ROL ──────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_rol') {

        $id_objetivo = intval($_POST['id_usuario'] ?? 0);
        $nuevo_rol   = $_POST['nuevo_rol'] ?? '';
        $roles_validos = ['registrado', 'moderador', 'gestor', 'superusuario'];

        if (!in_array($nuevo_rol, $roles_validos)) {
            $error = "Rol no válido.";

        } elseif ($id_objetivo === intval($_SESSION['usuario_id']) && $nuevo_rol !== 'superusuario') {
            // El superusuario no puede quitarse su propio rol si es el único
            $res   = $mysqli->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'superusuario'");
            $total = $res->fetch_assoc()['total'];
            if ($total <= 1) {
                $error = "No puedes cambiar tu propio rol: eres el único superusuario del sistema.";
            }
        }

        if (!$error) {
            // Si se quiere quitar el rol de superusuario a otro, comprobar que queda al menos uno
            $stmt = $mysqli->prepare("SELECT rol FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $id_objetivo);
            $stmt->execute();
            $objetivo = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($objetivo && $objetivo['rol'] === 'superusuario' && $nuevo_rol !== 'superusuario') {
                $res   = $mysqli->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'superusuario'");
                $total = $res->fetch_assoc()['total'];
                if ($total <= 1) {
                    $error = "No puedes quitar el rol de superusuario: es el único que queda en el sistema.";
                }
            }

            if (!$error) {
                $stmt = $mysqli->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
                $stmt->bind_param("si", $nuevo_rol, $id_objetivo);
                $stmt->execute();
                $stmt->close();

                // Si el superusuario se cambia su propio rol a superusuario (sin cambio), ok
                // Si se cambia a sí mismo actualizamos la sesión
                if ($id_objetivo === intval($_SESSION['usuario_id'])) {
                    $_SESSION['usuario_rol'] = $nuevo_rol;
                    $twig->addGlobal('sesion_rol', $nuevo_rol);
                }

                $exito = "Rol actualizado correctamente.";
            }
        }
    }

    // ── LISTAR USUARIOS ──────────────────────────────────────────────────────
    $usuarios = $mysqli->query(
        "SELECT id, nombre, email, rol, fecha_registro
         FROM usuarios
         ORDER BY
            FIELD(rol, 'superusuario', 'gestor', 'moderador', 'registrado'),
            nombre ASC"
    )->fetch_all(MYSQLI_ASSOC);

    echo $twig->render('gestion_usuarios.html.twig', [
        'usuarios'    => $usuarios,
        'error'       => $error,
        'exito'       => $exito,
        'mi_id'       => intval($_SESSION['usuario_id'])
    ]);
?>
