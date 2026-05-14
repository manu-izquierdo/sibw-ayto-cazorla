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
docker-compose up -d --build
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
Gestión de contenidos:
- Sistema usuarios con 5 roles
- Login/Logout
- CRUD noticias
- Moderación comentarios
- Panel administrativo

### ⌛ Práctica 5:
...

---

## Base de Datos

Tablas principales:
- **noticias** - Títulos, descripciones, fecha
- **imagenes** - Rutas de fotos por noticia
- **comentarios** - Nombre, email, texto
- **lugares** - Localidades para resaltado
- **usuarios** (P4) - Autenticación y roles

---

## Seguridad

✅ Prepared statements (evita SQL injection)  
✅ Validación servidor  
✅ Contraseñas hash  
✅ Sesiones PHP  

---

## Estructura Básica

```
sibw-ayto-cazorla/
├── www/
│   ├── index.php (router)
│   ├── bd.php (conexión BD)
│   ├── css/ (estilos)
│   ├── js/ (javascript)
│   ├── templates/ (Twig)
│   └── database/ (SQL)
├── config/ (configuración)
├── docker-compose.yml
└── README.md
```

---

## Inicio de Sesión (Práctica 4)

Usuarios de prueba en `updatedata.sql`

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

✅ Prácticas 1-3 completadas  
⏳ Práctica 4 en desarrollo

---

## Licencia

MIT License - Libre para usar, modificar y distribuir