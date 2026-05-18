<?php

function listarUsuarios() {
    $mysqli = conectar();

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
    $mysqli->close();

    return $usuarios;
}

function obtenerRolDeUsuario($id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT rol FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    return $fila ? $fila['rol'] : null;
}

function contarSuperusuarios() {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'superusuario'"
    );
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    $mysqli->close();

    return intval($total);
}

function cambiarRolUsuario($id, $nuevo_rol) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_rol, $id);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}

function borrarUsuarioPorId($id) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $resultado;
}