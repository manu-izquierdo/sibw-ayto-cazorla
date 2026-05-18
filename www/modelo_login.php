<?php

function obtenerUsuarioPorEmail($email) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "SELECT id, nombre, password, rol FROM usuarios WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();

    return $usuario;
}