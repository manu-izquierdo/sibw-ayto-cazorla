DROP USER IF EXISTS 'manu_sibw'@'%';
-- 1. Crear el usuario (si no existe) con permisos de acceso remoto
-- Usamos 'mysql_native_password' para evitar problemas de compatibilidad con PHP
CREATE USER IF NOT EXISTS 'manu_sibw'@'%' IDENTIFIED WITH mysql_native_password BY 'practica3';

-- 2. Darle todos los permisos sobre la base de datos específica
GRANT ALL PRIVILEGES ON sibw.* TO 'manu_sibw'@'%';

-- 3. Aplicar los cambios
FLUSH PRIVILEGES;

USE sibw;

-- 1. Desactivar comprobación de llaves foráneas para poder limpiar en cascada
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Limpiar datos existentes
DELETE FROM comentarios;
DELETE FROM imagenes;
DELETE FROM noticias;
DELETE FROM lugares;
DELETE FROM noticia_hashtag;
DELETE FROM hashtags;
DELETE FROM usuarios;

-- 3. Reiniciar los contadores de Auto Incremento
ALTER TABLE comentarios AUTO_INCREMENT = 1;
ALTER TABLE imagenes AUTO_INCREMENT = 1;
ALTER TABLE noticias AUTO_INCREMENT = 1;
ALTER TABLE lugares AUTO_INCREMENT = 1;
ALTER TABLE noticia_hashtag AUTO_INCREMENT = 1;
ALTER TABLE hashtags AUTO_INCREMENT = 1;
ALTER TABLE usuarios AUTO_INCREMENT = 1;

-- 4. Reactivar comprobaciones
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- POBLADO DE DATOS
-- ==============================================================================

-- LUGARES
INSERT INTO lugares VALUES 
(1,'Cazorla'),(2,'La Iruela'),(3,'Arroyo Frío'),(4,'Burunchel'),
(5,'Quesada'),(6,'Chilluévar'),(7,'Peal de Becerro'),(8,'Santo Tomé'),
(9,'Pozo Alcón'),(10,'Hinojares'),(11,'Huesa'),(12,'Coto Ríos'),(13,'Vadillo Castril');

-- NOTICIAS
INSERT INTO noticias VALUES 
(1,'Corte de tráfico temporal por la caída de un pino','2026-04-05 19:23:17','Incidente Tráfico','Urbanismo',1,'<p>El Ayuntamiento de Cazorla informa a todos los vecinos y visitantes que la carretera A-319, en el tramo de subida hacia el Empalme del Valle, permanecerá cortada al tráfico durante la mañana de hoy debido a la caída de un pino de grandes dimensiones sobre la calzada, provocada por las fuertes rachas de viento de la pasada madrugada.</p><p>Afortunadamente, no hay que lamentar daños personales ni materiales de gravedad en los vehículos que transitaban la zona. Efectivos de la Policía Local, junto con los servicios de mantenimiento del Parque Natural y los Bomberos, ya se encuentran en el lugar trabajando en las labores de troceado y retirada del tronco.</p><p>Se recomienda a los conductores que necesiten acceder a la sierra que utilicen rutas alternativas y extremen la precaución, ya que la alerta amarilla por fuertes vientos seguirá activa hasta las 18:00 horas.</p>'),
(2,'XI Edición de la Ruta de la Tapa de Cazorla','2026-04-05 19:23:17','Evento Turístico','Turismo',1,'<p>Cazorla celebra este mes de abril su undécima Ruta de la Tapa, un evento consolidado que busca promocionar la gastronomía local y los productos de la zona. En esta edición participan un total de 15 establecimientos, ofreciendo propuestas que combinan la tradición serrana con técnicas de cocina vanguardista.</p><p>El sistema de votación se mantendrá mediante el tradicional "tapaporte", que podrá ser sellado en cada local participante. Aquellos comensales que logren completar al menos el 80% del recorrido entrarán en el sorteo de fines de semana en alojamientos rurales y lotes de aceite de oliva virgen extra de la Sierra de Cazorla.</p>'),
(3,'Modernización y mejora del Polideportivo Municipal','2026-04-05 19:23:17','Obras','Deportes',1,'<p>Han comenzado las obras de remodelación integral de las pistas exteriores del Polideportivo Municipal. El proyecto incluye la sustitución del césped artificial en las pistas de pádel, así como la renovación del pavimento de las canchas de tenis y baloncesto, que presentaban grietas debido al uso continuado y la exposición a la intemperie.</p><p>La inversión, cofinanciada por la Diputación de Jaén, asciende a 45.000 euros. Se estima que las instalaciones permanezcan cerradas durante tres semanas, previendo su reapertura total a principios del próximo mes.</p>'),
(4,'Aviso de interrupción del suministro de agua en el Casco Antiguo','2026-04-05 19:23:17','Aviso','Infraestructuras',1,'<p>La empresa concesionaria del servicio de aguas informa de un corte programado para el próximo miércoles, 8 de abril, que afectará a las calles aledañas a la Plaza de Santa María y el Camino de la Ermita. El motivo es la reparación de una tubería general que presenta una fuga importante detectada por los servicios técnicos.</p><p>El corte se iniciará a las 08:00 horas y se prevé que el servicio se restablezca paulatinamente a partir de las 14:00 horas. Se ruega a los vecinos que tomen las medidas de precaución necesarias.</p>'),
(5,'Nueva Sala de Estudio en la Biblioteca Municipal','2026-04-05 19:23:17','Cultura','Educación',1,'<p>Ante la creciente demanda de los estudiantes locales, el Ayuntamiento ha habilitado una nueva sala de estudio en la planta superior de la Biblioteca Municipal. Este espacio cuenta con 25 puestos adicionales equipados con conexión eléctrica individual y una mejora sustancial en la red Wi-Fi del edificio.</p><p>El horario se verá ampliado durante los periodos de exámenes, abriendo de forma ininterrumpida desde las 08:00 hasta las 22:00 horas, facilitando así la preparación académica de los jóvenes cazorleños.</p>'),
(6,'Plan de Asfaltado y Mejora de Vías Urbanas','2026-04-05 19:23:17','Urbanismo','Obras Públicas',2,'<p>Se ha puesto en marcha la segunda fase del Plan de Mejora de Vías Urbanas en el sector norte de la localidad. Las actuaciones se centran en el reasfaltado de las calles que conectan el centro histórico con las nuevas zonas de expansión, buscando eliminar baches y mejorar la seguridad vial para conductores y peatones.</p><p>Durante la ejecución de las obras se producirán desvíos provisionales de tráfico que estarán debidamente señalizados. El Ayuntamiento agradece la comprensión de los ciudadanos por las molestias temporales.</p>'),
(7,'Campaña "Cazorla Recicla" para el fomento de la sostenibilidad','2026-04-05 19:23:17','Medio Ambiente','Sostenibilidad',3,'<p>La Concejalía de Medio Ambiente inicia una nueva campaña informativa para concienciar sobre la importancia de la separación de residuos en origen. Se distribuirán kits de reciclaje doméstico y se instalarán nuevos contenedores para la recogida de aceite usado y pilas en puntos estratégicos del municipio.</p><p>Cazorla sigue comprometida con la protección del Parque Natural que nos rodea, y la correcta gestión de los residuos urbanos es un pilar fundamental para mantener nuestro ecosistema saludable.</p>'),
(8,'Ayudas directas para la digitalización del comercio local','2026-04-05 19:23:17','Economía','Comercio',1,'<p>El Ayuntamiento abre el plazo de solicitud para las ayudas directas destinadas a la digitalización del pequeño comercio. Estas subvenciones cubren hasta el 70% de la inversión en creación de páginas web, sistemas de venta online y gestión de redes sociales profesionales.</p><p>El objetivo es dotar a nuestros comerciantes de las herramientas necesarias para competir en el mercado digital y atraer a un público más joven y global.</p>');

-- IMÁGENES
INSERT INTO imagenes VALUES 
(1,1,'img/Noticias/n1_1.png'),
(2,1,'img/Noticias/n1_2.png'),
(3,1,'img/Noticias/n1_3.png'),
(4,1,'img/Noticias/n1_4.png'),
(5,1,'img/Noticias/n1_5.png'),
(6,2,'img/Noticias/n2_1.png'),
(7,2,'img/Noticias/n2_2.png'),
(8,3,'img/Noticias/n3_1.png'),
(9,4,'img/Noticias/n4_1.png'),
(10,5,'img/Noticias/n5_1.png'),
(11,6,'img/Noticias/n6_1.png'),
(12,7,'img/Noticias/n7_1.png'),
(13,8,'img/Noticias/n8_1.png');

-- COMENTARIOS (con la nueva columna 'editado')
INSERT INTO comentarios (noticia_id, nombre, email, texto, fecha, editado) VALUES 
(1, 'Manu Izquierdo', 'local@cazorla.es', 'Espero que abran la carretera pronto, gracias por el aviso.', '2026-04-20 08:15:00', 0),
(2, 'Turista', 'visita@correo.com', 'Excelente iniciativa. Este año iremos a LA IRUELA y pasaremos por la ruta.', '2026-04-23 09:00:00', 0);

-- ==============================================================================
-- USUARIOS DE PRUEBA (contraseñas hasheadas con password_hash en PHP)
-- Contraseña real de todos los usuarios de prueba: "1234"
-- Hash generado con: password_hash("1234", PASSWORD_DEFAULT)
-- ==============================================================================
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Ana Registrada',    'registrado@cazorla.es',   '$2y$10$wFzaKhtgZDIAFK7MwVE2XO4/6yefjJCswWxJmNRcIZqgHdf3egqxm', 'registrado'),
('Pedro Moderador',   'moderador@cazorla.es',    '$2y$10$wFzaKhtgZDIAFK7MwVE2XO4/6yefjJCswWxJmNRcIZqgHdf3egqxm', 'moderador'),
('Laura Gestora',     'gestor@cazorla.es',       '$2y$10$wFzaKhtgZDIAFK7MwVE2XO4/6yefjJCswWxJmNRcIZqgHdf3egqxm', 'gestor'),
('Admin Super',       'admin@cazorla.es',        '$2y$10$wFzaKhtgZDIAFK7MwVE2XO4/6yefjJCswWxJmNRcIZqgHdf3egqxm', 'superusuario');

-- HASHTAGS de ejemplo
INSERT INTO hashtags (nombre) VALUES
('obras'),
('turismo'),
('cultura'),
('medioambiente'),
('comercio'),
('deportes'),
('infraestructuras');

-- RELACIÓN noticias ↔ hashtags
INSERT INTO noticia_hashtag (noticia_id, hashtag_id) VALUES
(1, 1),  -- corte tráfico → obras
(2, 2),  -- ruta tapa → turismo
(3, 1),  -- polideportivo → obras
(3, 6),  -- polideportivo → deportes
(4, 7),  -- agua → infraestructuras
(5, 3),  -- biblioteca → cultura
(6, 1),  -- asfaltado → obras
(7, 4),  -- reciclaje → medioambiente
(8, 5);  -- digitalización → comercio