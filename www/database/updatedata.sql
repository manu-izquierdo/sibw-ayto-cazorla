USE sibw;

-- 1. Desactivar comprobación de llaves foráneas para poder limpiar
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Limpiar tablas y reiniciar contadores (AUTO_INCREMENT)
DELETE FROM imagenes;
DELETE FROM noticias;
ALTER TABLE noticias AUTO_INCREMENT = 1;
ALTER TABLE imagenes AUTO_INCREMENT = 1;

-- 3. Reactivar comprobaciones
SET FOREIGN_KEY_CHECKS = 1;

-- Inserción de 8 noticias detalladas
INSERT INTO noticias (id, titulo, tipo, concejalia, descripcion, lugar_id) VALUES 
(1, 'Corte de tráfico temporal por la caída de un pino', 'Incidente Tráfico', 'Urbanismo', 
'<p>El Ayuntamiento de Cazorla informa a todos los vecinos y visitantes que la carretera A-319, en el tramo de subida hacia el Empalme del Valle, permanecerá cortada al tráfico durante la mañana de hoy debido a la caída de un pino de grandes dimensiones sobre la calzada, provocada por las fuertes rachas de viento de la pasada madrugada.</p><p>Afortunadamente, no hay que lamentar daños personales ni materiales de gravedad en los vehículos que transitaban la zona. Efectivos de la Policía Local, junto con los servicios de mantenimiento del Parque Natural y los Bomberos, ya se encuentran en el lugar trabajando en las labores de troceado y retirada del tronco.</p><p>Se recomienda a los conductores que necesiten acceder a la sierra que utilicen rutas alternativas y extremen la precaución, ya que la alerta amarilla por fuertes vientos seguirá activa hasta las 18:00 horas.</p>', 1),

(2, 'XI Edición de la Ruta de la Tapa de Cazorla', 'Evento Turístico', 'Turismo', 
'<p>Cazorla celebra este mes de abril su undécima Ruta de la Tapa, un evento consolidado que busca promocionar la gastronomía local y los productos de la zona. En esta edición participan un total de 15 establecimientos, ofreciendo propuestas que combinan la tradición serrana con técnicas de cocina vanguardista.</p><p>El sistema de votación se mantendrá mediante el tradicional "tapaporte", que podrá ser sellado en cada local participante. Aquellos comensales que logren completar al menos el 80% del recorrido entrarán en el sorteo de fines de semana en alojamientos rurales y lotes de aceite de oliva virgen extra de la Sierra de Cazorla.</p>', 1),

(3, 'Modernización y mejora del Polideportivo Municipal', 'Obras', 'Deportes', 
'<p>Han comenzado las obras de remodelación integral de las pistas exteriores del Polideportivo Municipal. El proyecto incluye la sustitución del césped artificial en las pistas de pádel, así como la renovación del pavimento de las canchas de tenis y baloncesto, que presentaban grietas debido al uso continuado y la exposición a la intemperie.</p><p>La inversión, cofinanciada por la Diputación de Jaén, asciende a 45.000 euros. Se estima que las instalaciones permanezcan cerradas durante tres semanas, previendo su reapertura total a principios del próximo mes.</p>', 1),

(4, 'Aviso de interrupción del suministro de agua en el Casco Antiguo', 'Aviso', 'Infraestructuras', 
'<p>La empresa concesionaria del servicio de aguas informa de un corte programado para el próximo miércoles, 8 de abril, que afectará a las calles aledañas a la Plaza de Santa María y el Camino de la Ermita. El motivo es la reparación de una tubería general que presenta una fuga importante detectada por los servicios técnicos.</p><p>El corte se iniciará a las 08:00 horas y se prevé que el servicio se restablezca paulatinamente a partir de las 14:00 horas. Se ruega a los vecinos que tomen las medidas de precaución necesarias.</p>', 1),

(5, 'Nueva Sala de Estudio en la Biblioteca Municipal', 'Cultura', 'Educación', 
'<p>Ante la creciente demanda de los estudiantes locales, el Ayuntamiento ha habilitado una nueva sala de estudio en la planta superior de la Biblioteca Municipal. Este espacio cuenta con 25 puestos adicionales equipados con conexión eléctrica individual y una mejora sustancial en la red Wi-Fi del edificio.</p><p>El horario se verá ampliado durante los periodos de exámenes, abriendo de forma ininterrumpida desde las 08:00 hasta las 22:00 horas, facilitando así la preparación académica de los jóvenes cazorleños.</p>', 1),

(6, 'Plan de Asfaltado y Mejora de Vías Urbanas', 'Urbanismo', 'Obras Públicas', 
'<p>Se ha puesto en marcha la segunda fase del Plan de Mejora de Vías Urbanas en el sector norte de la localidad. Las actuaciones se centran en el reasfaltado de las calles que conectan el centro histórico con las nuevas zonas de expansión, buscando eliminar baches y mejorar la seguridad vial para conductores y peatones.</p><p>Durante la ejecución de las obras se producirán desvíos provisionales de tráfico que estarán debidamente señalizados. El Ayuntamiento agradece la comprensión de los ciudadanos por las molestias temporales.</p>', 2),

(7, 'Campaña "Cazorla Recicla" para el fomento de la sostenibilidad', 'Medio Ambiente', 'Sostenibilidad', 
'<p>La Concejalía de Medio Ambiente inicia una nueva campaña informativa para concienciar sobre la importancia de la separación de residuos en origen. Se distribuirán kits de reciclaje doméstico y se instalarán nuevos contenedores para la recogida de aceite usado y pilas en puntos estratégicos del municipio.</p><p>Cazorla sigue comprometida con la protección del Parque Natural que nos rodea, y la correcta gestión de los residuos urbanos es un pilar fundamental para mantener nuestro ecosistema saludable.</p>', 3),

(8, 'Ayudas directas para la digitalización del comercio local', 'Economía', 'Comercio', 
'<p>El Ayuntamiento abre el plazo de solicitud para las ayudas directas destinadas a la digitalización del pequeño comercio. Estas subvenciones cubren hasta el 70% de la inversión en creación de páginas web, sistemas de venta online y gestión de redes sociales profesionales.</p><p>El objetivo es dotar a nuestros comerciantes de las herramientas necesarias para competir en el mercado digital y atraer a un público más joven y global.</p>', 1);

-- Inserción de imágenes asociadas
INSERT INTO imagenes (noticia_id, ruta) VALUES 
(1, 'img/noticia.png'), (1, 'img/noticia2.png'),
(2, 'img/noticia.png'), (2, 'img/noticia2.png'),
(3, 'img/noticia.png'), (3, 'img/noticia2.png'),
(4, 'img/noticia.png'), (4, 'img/noticia2.png'),
(5, 'img/noticia.png'), (5, 'img/noticia2.png'),
(6, 'img/noticia.png'), (6, 'img/noticia2.png'),
(7, 'img/noticia.png'), (7, 'img/noticia2.png'),
(8, 'img/noticia.png'), (8, 'img/noticia2.png');