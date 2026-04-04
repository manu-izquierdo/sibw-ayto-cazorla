CREATE DATABASE IF NOT EXISTS sibw;
USE sibw;

-- Tabla para el script de censura/resaltado y campo 'lugar'
CREATE TABLE IF NOT EXISTS lugares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Tabla principal de noticias
CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(100),
    concejalia VARCHAR(100),
    personas TEXT,
    lugar_id INT,
    descripcion TEXT,
    FOREIGN KEY (lugar_id) REFERENCES lugares(id)
);

-- Galería de imágenes (1 noticia : N imágenes)
CREATE TABLE IF NOT EXISTS imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    noticia_id INT NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE
);

-- Comentarios vinculados a noticias
CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    noticia_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    texto TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE
);