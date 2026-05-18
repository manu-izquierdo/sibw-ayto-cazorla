<?php

function emailExiste($email) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $existe = $stmt->num_rows > 0;
    $stmt->close();
    $mysqli->close();

    return $existe;
}

function insertarUsuario($nombre, $email, $hash) {
    $mysqli = conectar();

    $stmt = $mysqli->prepare(
        "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'registrado')"
    );
    $stmt->bind_param("sss", $nombre, $email, $hash);
    $resultado = $stmt->execute();
    $nuevo_id  = $mysqli->insert_id;
    $stmt->close();
    $mysqli->close();

    return $resultado ? $nuevo_id : false;
}