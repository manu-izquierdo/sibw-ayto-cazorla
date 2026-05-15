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
    editado TINYINT(1) DEFAULT 0,
    FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE
);

-- ==============================================================================
-- NUEVAS TABLAS - PRÁCTICA 4
-- ==============================================================================
 
-- Tabla de usuarios con roles
-- Los roles disponibles son:
--   registrado  → puede comentar y editar sus propios datos
--   moderador   → puede editar/borrar comentarios
--   gestor      → puede añadir/editar/borrar noticias y hashtags
--   superusuario→ puede hacer todo, incluyendo gestionar roles
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,           -- Almacenado con password_hash()
    rol ENUM('registrado','moderador','gestor','superusuario') NOT NULL DEFAULT 'registrado',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);
 
-- Hashtags únicos (un mismo hashtag puede asociarse a varias noticias)
CREATE TABLE IF NOT EXISTS hashtags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE       -- Ej: "obras", "cultura", "turismo"
);
 
-- Tabla pivote que relaciona noticias ↔ hashtags (N:M)
CREATE TABLE IF NOT EXISTS noticia_hashtag (
    noticia_id INT NOT NULL,
    hashtag_id INT NOT NULL,
    PRIMARY KEY (noticia_id, hashtag_id),
    FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE,
    FOREIGN KEY (hashtag_id) REFERENCES hashtags(id) ON DELETE CASCADE
);
 