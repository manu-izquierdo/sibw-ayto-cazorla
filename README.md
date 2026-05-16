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

### Con XAMPP
```bash
# Copiar a C:\xampp\htdocs\sibw-ayto-cazorla
# Iniciar Apache + MySQL
# Importar: www/database/setup.sql
# Acceder: http://localhost/sibw-ayto-cazorla
```

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
- Manipulación del DOM

### Práctica 3: Backend (PHP + BD) - 30%
Sistema dinámico:
- Conexión MySQL
- Patrón MVC con Twig
- URLs limpias
- Validación servidor (SQL injection prevention)
- Galería de imágenes

### Práctica 4: Autenticación (PHP II) - 30%
Gestión de contenidos con sistema de roles:
- **Usuarios y sesiones**: registro, login/logout y perfil editable con contraseñas hasheadas (`password_hash`)
- **5 roles**: anónimo, registrado, moderador, gestor y superusuario
- **Moderación**: editar y borrar comentarios desde la noticia o desde el panel `/comentarios`, con badge "Mensaje editado por el moderador"
- **Gestión de noticias**: CRUD completo desde la noticia o desde el panel `/noticias`, con buscador doble (título + descripción)
- **Hashtags**: asociación N:M con noticias, visibles como etiquetas en cada noticia
- **Imágenes**: selección desde la carpeta `img/Noticias/` sin subida de archivos
- **Gestión de usuarios**: el superusuario puede cambiar roles y eliminar usuarios, con protección para que nunca quede el sistema sin superusuario
- **Control de acceso**: verificación de rol tanto en cliente (menú dinámico) como en servidor (cabecera de cada controlador)

### ⌛ Práctica 5:
...

---

## Base de Datos

Tablas principales:
- **noticias** - Títulos, descripciones, fecha
- **imagenes** - Rutas de fotos por noticia
- **comentarios** - Nombre, email, texto, flag `editado`
- **lugares** - Localidades para resaltado
- **usuarios** - Autenticación y roles (P4)
- **hashtags** - Etiquetas únicas (P4)
- **noticia_hashtag** - Relación N:M noticias ↔ hashtags (P4)

---

## Seguridad

✅ Prepared statements (evita SQL injection)  
✅ Validación y sanitización en servidor  
✅ Contraseñas hasheadas con `password_hash`  
✅ Sesiones PHP con verificación de rol  
✅ Control de acceso server-side en cada controlador  

---

## Estructura Básica

```
sibw-ayto-cazorla/
├── www/
│   ├── index.php (router)
│   ├── bd.php (conexión BD + session_start)
│   ├── controlador_*.php
│   ├── css/ (estilos)
│   ├── js/ (javascript)
│   ├── templates/ (Twig)
│   └── database/ (SQL)
├── config/ (configuración)
├── docker-compose.yml
└── README.md
```

---

## Usuarios de prueba (P4)

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
# Ver logs Docker
docker-compose logs -f webserver

# Reiniciar
docker-compose down && docker-compose up -d --build

# BD manualmente
docker exec lamp-mysql8 mysql -u root -proot < www/database/setup.sql
```

---

## Estado

✅ Prácticas 1-4 completadas  
⏳ Práctica 5 pendiente

---

## Licencia

MIT License - Libre para usar, modificar y distribuir
