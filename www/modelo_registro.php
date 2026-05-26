<?php

function emailExiste($mysqli, $email) {
    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $existe = $stmt->num_rows > 0;
    $stmt->close();

    return $existe;
}

function insertarUsuario($mysqli, $nombre, $email, $hash) {
    $stmt = $mysqli->prepare(
        "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'registrado')"
    );
    $stmt->bind_param("sss", $nombre, $email, $hash);
    $resultado = $stmt->execute();
    $nuevo_id  = $mysqli->insert_id;
    $stmt->close();

    return $resultado ? $nuevo_id : false;
}
