<?php
    // Destruimos todos los datos de sesión y redirigimos a la portada
    $_SESSION = [];
    session_destroy();

    header("Location: /");
    exit;
?>
