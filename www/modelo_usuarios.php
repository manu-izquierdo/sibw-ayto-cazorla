<?php

function listarUsuarios($mysqli) {
    $stmt = $mysqli->prepare(
        "SELECT id, nombre, email, rol, fecha_registro
         FROM usuarios
         ORDER BY
            FIELD(rol, 'superusuario', 'gestor', 'moderador', 'registrado'),
            nombre ASC"
    );
    $stmt->execute();
    $usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $usuarios;
}

function obtenerRolDeUsuario($mysqli, $id) {
    $stmt = $mysqli->prepare("SELECT rol FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $fila ? $fila['rol'] : null;
}

function contarSuperusuarios($mysqli) {
    $stmt = $mysqli->prepare(
        "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'superusuario'"
    );
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    return intval($total);
}

function cambiarRolUsuario($mysqli, $id, $nuevo_rol) {
    $stmt = $mysqli->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_rol, $id);
    $resultado = $stmt->execute();
    $stmt->close();

    return $resultado;
}

function borrarUsuarioPorId($mysqli, $id) {
    $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();
    $stmt->close();

    return $resultado;
}
