# 🏛️ SIBW - Portal de Incidencias Cazorla

**Proyecto académico SIBW** | 2º Ingeniería Informática UGR | Prof. Carmen Martínez Cruz

---

## ¿Qué es?

Portal web para gestionar noticias e incidencias del Ayuntamiento de Cazorla. Sistema dinámico con usuarios, comentarios, y panel administrativo.

---

## Instalación Rápida

### Con Docker
```bash
git clone https://github.com/manu-izquierdo/sibw-ayto-cazorla.git
cd sibw-ayto-cazorla
cp sample.env .env
docker compose up -d --build
# Acceder: http://localhost
```

Tras arrancar los contenedores, dar permisos de escritura al directorio de imágenes:
```bash
docker exec lamp-php84 chmod 775 /var/www/html/img/Noticias
docker exec lamp-php84 chown www-data:www-data /var/www/html/img/Noticias
```

### Importar la base de datos (phpMyAdmin)

Tras arrancar los contenedores, accede a **http://localhost:8080** (phpMyAdmin).  
Importa los dos ficheros SQL en este orden:

1. `www/database/setup.sql` — crea las tablas
2. `www/database/updatedata.sql` — inserta los datos de prueba y usuarios

---

## Stack

**Frontend**: HTML5, CSS3, JavaScript  
**Backend**: PHP 8.4  
**BD**: MySQL 8.0 / MariaDB  
**Templates**: Twig 3.0  
**Servidor**: Apache 2.4 + Docker

---

## Prácticas

### Práctica 1: Diseño (HTML/CSS) - 20%
Creación del sitio estático con:
- Portada con grid de noticias
- Página de detalle de noticia
- Diseño responsivo (Flexbox + Grid)
- Versión impresión en B/N

### Práctica 2: Interactividad (JavaScript) - 10%
Componentes dinámicos:
- Panel comentarios deslizante
- Validación formulario (email con regex)
- Resaltado automático de localidades

### Práctica 3: Backend (PHP + BD) - 30%
Sistema dinámico:
- Conexiones no persistentes (función `conectar()` en `bd.php`); el controlador abre la conexión, la pasa a cada función del modelo como parámetro y la cierra al finalizar — una sola conexión por petición HTTP
- Separación Modelo-Controlador (`modelo_*.php` / `controlador_*.php`)
- URLs limpias vía `.htaccess` y router en `index.php`
- Validación servidor con prepared statements (prevención SQL injection)
- Galería de imágenes por noticia

### Práctica 4: Autenticación (PHP II) - 30%
Gestión de contenidos con sistema de roles:
- **Usuarios y sesiones**: registro, login/logout y perfil editable con contraseñas hasheadas (`password_hash` / `password_verify`)
- **5 roles**: anónimo, registrado, moderador, gestor y superusuario
- **Moderación**: editar y borrar comentarios desde la noticia o desde el panel `/comentarios`, con badge "Mensaje editado por el moderador"
- **Gestión de noticias**: CRUD completo desde la noticia o desde el panel `/noticias`, con buscador triple (título, descripción y hashtag)
- **Hashtags**: asociación N:M con noticias, visibles como etiquetas en cada noticia; gestión completa desde `/hashtags`
- **Imágenes**: subida real de archivos con `move_uploaded_file()` al directorio `img/Noticias/`; borrado físico del disco al eliminar imagen o noticia
- **Gestión de usuarios**: el superusuario puede cambiar roles y eliminar usuarios, con protección para que nunca quede el sistema sin superusuario
- **Control de acceso**: verificación de rol tanto en cliente (menú dinámico Twig) como en servidor (cabecera de cada controlador)

### Práctica 5: AJAX - 10%
Funcionalidades asíncronas:
- **Búsqueda dinámica en portada**: campo estilo Google con dropdown de resultados en tiempo real; al hacer clic en un resultado navega a la noticia; solo muestra noticias publicadas
- **Búsqueda dinámica en gestión de noticias**: los buscadores existentes (título, descripción, hashtag) actualizan la tabla sin recargar la página, con búsqueda instantánea al escribir
- **Estado publicado/no publicado**: campo `publicado TINYINT(1)` en la tabla `noticias`; solo las noticias publicadas aparecen en portada
- **Toggle publicado con AJAX**: checkbox en el panel de gestión que cambia el estado de publicación en BD de forma instantánea sin recarga
- **Endpoint JSON**: `controlador_ajax.php` (ruta `/ajax`) centraliza todas las peticiones AJAX, devuelve JSON y valida sesión cuando es necesario
- **Peticiones con `async/await` + `fetch`**: patrón uniforme en todo el JS siguiendo el tutorial de la asignatura

---

## Base de Datos

Tablas principales:
- **noticias** - Títulos, descripciones, fecha, tipo, concejalía, lugar, `publicado` (P5)
- **imagenes** - Rutas de fotos por noticia (1:N, borrado en cascada)
- **comentarios** - Nombre, email, texto, flag `editado` (borrado en cascada)
- **lugares** - Localidades para resaltado automático en comentarios
- **usuarios** - Autenticación y roles (P4)
- **hashtags** - Etiquetas únicas (P4)
- **noticia_hashtag** - Relación N:M noticias ↔ hashtags (P4, borrado en cascada)

---

## Seguridad

✅ Prepared statements en todas las consultas (evita SQL injection)  
✅ Validación y sanitización en servidor (`intval`, `trim`, `filter_var`)  
✅ Contraseñas hasheadas con `password_hash` / verificadas con `password_verify`  
✅ Sesiones PHP con verificación de rol al inicio de cada controlador  
✅ Control de acceso server-side (redirige a `/` si sin permisos)  
✅ Sin JavaScript inline en plantillas Twig  
✅ Borrado físico de archivos de imagen al eliminar noticias o imágenes  
✅ Endpoint AJAX protegido por sesión para operaciones de escritura  

---

## Estructura

```
sibw-ayto-cazorla/
├── www/
│   ├── index.php               ← Router principal (Front Controller)
│   ├── bd.php                  ← Conexión no persistente + session_start()
│   ├── .htaccess               ← URLs limpias
│   ├── modelo_portada.php      ← obtenerNoticias() + buscarNoticiasPublicadas() (P5)
│   ├── modelo_noticia.php
│   ├── modelo_imprimir.php
│   ├── modelo_login.php
│   ├── modelo_registro.php
│   ├── modelo_perfil.php
│   ├── modelo_comentarios.php
│   ├── modelo_noticias.php     ← CRUD + subida de imágenes + hashtags + togglePublicado() (P5)
│   ├── modelo_hashtags.php
│   ├── modelo_usuarios.php
│   ├── controlador_portada.php
│   ├── controlador_noticia.php
│   ├── controlador_imprimir.php
│   ├── controlador_login.php
│   ├── controlador_registro.php
│   ├── controlador_logout.php
│   ├── controlador_perfil.php
│   ├── controlador_comentarios.php
│   ├── controlador_noticias.php
│   ├── controlador_hashtags.php
│   ├── controlador_usuarios.php
│   ├── controlador_ajax.php    ← Endpoint JSON para peticiones AJAX (P5)
│   ├── css/
│   │   ├── style.css
│   │   └── auth.css
│   ├── js/
│   │   └── script.js           ← Todo el JS (sin inline en Twig)
│   ├── img/
│   │   └── Noticias/           ← Imágenes subidas por los gestores
│   ├── templates/
│   │   ├── base.html.twig
│   │   ├── portada.html.twig
│   │   ├── noticia.html.twig
│   │   ├── noticia_imprimir.html.twig
│   │   ├── login.html.twig
│   │   ├── registro.html.twig
│   │   ├── perfil.html.twig
│   │   ├── gestion_comentarios.html.twig
│   │   ├── gestion_noticias.html.twig
│   │   ├── formulario_noticia.html.twig
│   │   ├── gestion_hashtags.html.twig
│   │   └── gestion_usuarios.html.twig
│   └── database/
│       ├── setup.sql           ← Creación de tablas
│       └── updatedata.sql      ← Datos de prueba + usuarios
├── config/
├── docker-compose.yml
└── README.md
```

---

## Rutas disponibles

| Ruta | Acceso | Descripción |
|---|---|---|
| `/` | Todos | Portada con noticias publicadas y buscador AJAX |
| `/noticia/{id}` | Todos | Página de noticia individual |
| `/imprimir/{id}` | Todos | Vista de impresión en B/N |
| `/login` | Anónimo | Formulario de login |
| `/registro` | Anónimo | Formulario de registro |
| `/logout` | Logueado | Cierre de sesión |
| `/perfil` | Logueado | Editar datos y eliminar cuenta |
| `/comentarios` | Moderador / Superusuario | Panel de gestión de comentarios |
| `/noticias` | Gestor / Superusuario | Panel de gestión de noticias con búsqueda AJAX y toggle publicado |
| `/hashtags` | Gestor / Superusuario | Panel de gestión de hashtags |
| `/usuarios` | Superusuario | Panel de gestión de usuarios y roles |
| `/ajax/buscar` | Según tipo | Búsqueda JSON para portada y gestión |
| `/ajax/toggle-publicado` | Gestor / Superusuario | Cambia estado publicado de una noticia |

---

## Usuarios de prueba

Contraseña de todos: `1234`

| Email | Rol |
|---|---|
| registrado@cazorla.es | registrado |
| moderador@cazorla.es | moderador |
| gestor@cazorla.es | gestor |
| admin@cazorla.es | superusuario |

---

## Troubleshooting

```bash
# Ver logs del servidor web
docker compose logs -f webserver

# Reiniciar contenedores
docker compose down && docker compose up -d --build

# Importar BD manualmente
docker exec lamp-mysql8 mysql -u root -proot sibw < www/database/setup.sql

# Reparar permisos de imágenes
docker exec lamp-php84 chmod 775 /var/www/html/img/Noticias
docker exec lamp-php84 chown www-data:www-data /var/www/html/img/Noticias
```

---

## Estado

✅ **Proyecto finalizado** — Prácticas 1-5 completadas (mayo 2026)

---

## Licencia

MIT License - Libre para usar, modificar y distribuir