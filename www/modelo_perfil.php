<?php

function obtenerUsuarioPorId($mysqli, $id) {
    $stmt = $mysqli->prepare(
        "SELECT nombre, email, rol, fecha_registro FROM usuarios WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $usuario;
}

function emailEnUso($mysqli, $email, $id_excluir) {
    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id_excluir);
    $stmt->execute();
    $stmt->store_result();
    $existe = $stmt->num_rows > 0;
    $stmt->close();

    return $existe;
}

function actualizarUsuario($mysqli, $id, $nombre, $email) {
    $stmt = $mysqli->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $email, $id);
    $resultado = $stmt->execute();
    $stmt->close();

    return $resultado;
}

function actualizarUsuarioConPassword($mysqli, $id, $nombre, $email, $hash) {
    $stmt = $mysqli->prepare(
        "UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE id = ?"
    );
    $stmt->bind_param("sssi", $nombre, $email, $hash, $id);
    $resultado = $stmt->execute();
    $stmt->close();

    return $resultado;
}

function eliminarUsuario($mysqli, $id) {
    $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();
    $stmt->close();

    return $resultado;
}
