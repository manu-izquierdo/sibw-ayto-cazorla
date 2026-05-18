<?php
    require_once "modelo_usuarios.php";

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

        if ($id_objetivo === intval($_SESSION['usuario_id'])) {
            $error = "No puedes eliminar tu propia cuenta desde aquí.";

        } else {
            $rol_objetivo = obtenerRolDeUsuario($id_objetivo);

            if ($rol_objetivo === 'superusuario' && contarSuperusuarios() <= 1) {
                $error = "No puedes eliminar al único superusuario del sistema.";
            } else {
                borrarUsuarioPorId($id_objetivo);
                $exito = "Usuario eliminado correctamente.";
            }
        }
    }

    // ── CAMBIAR ROL ──────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_rol') {

        $id_objetivo   = intval($_POST['id_usuario'] ?? 0);
        $nuevo_rol     = $_POST['nuevo_rol'] ?? '';
        $roles_validos = ['registrado', 'moderador', 'gestor', 'superusuario'];

        if (!in_array($nuevo_rol, $roles_validos)) {
            $error = "Rol no válido.";

        } else {
            $rol_actual = obtenerRolDeUsuario($id_objetivo);

            // Protección: no degradar si es el único superusuario
            if ($rol_actual === 'superusuario' && $nuevo_rol !== 'superusuario' && contarSuperusuarios() <= 1) {
                $error = "No puedes quitar el rol de superusuario: es el único que queda en el sistema.";
            } else {
                cambiarRolUsuario($id_objetivo, $nuevo_rol);

                // Si el superusuario se cambió su propio rol, actualizar sesión
                if ($id_objetivo === intval($_SESSION['usuario_id'])) {
                    $_SESSION['usuario_rol'] = $nuevo_rol;
                    $twig->addGlobal('sesion_rol', $nuevo_rol);
                }

                $exito = "Rol actualizado correctamente.";
            }
        }
    }

    echo $twig->render('gestion_usuarios.html.twig', [
        'usuarios' => listarUsuarios(),
        'error'    => $error,
        'exito'    => $exito,
        'mi_id'    => intval($_SESSION['usuario_id'])
    ]);
?>