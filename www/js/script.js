const panelLateral = document.querySelector('.formulario');
const zonaActiva = document.getElementById('zona-derecha');
const pestanaAbrir = document.getElementById('pestana-abrir');
const cerrar = document.getElementById('cancel');
const abrirFormulario = document.getElementById('boton-bloque-formulario');
const formulario = document.querySelector('.bloque-formulario');
const cerrarFormulario = document.getElementById('cancelar-comentario');
const inputNombre = document.getElementById('autor-comentario');
const inputEmail = document.getElementById('email-comentario');
const inputTexto = document.getElementById('texto-comentario');
const localidades = [
  "La Iruela",
  "Arroyo Frío",
  "Burunchel",
  "Quesada",
  "Peal de Becerro",
  "Santo Tomé",
  "Chilluévar",
  "Hinojares",
  "Pozo Alcón",
  "Hornos de Segura"
];

// Activa el panel lateral al acercar el ratón a la derecha de la pantalla
zonaActiva.addEventListener('mouseenter', function() {
    panelLateral.style.right = '0';
    abrirFormulario.style.display = 'flex';
});

// Activa el panel lateral al clicar en el botón 
pestanaAbrir.addEventListener("click", function() {
    panelLateral.style.right = '0';
    abrirFormulario.style.display = 'flex';
});


// Cuando se hace click sobre el boton con id 'cancel' (esquina superior izquierda botón X) se vuelve a ocultar el formulario
cerrar.addEventListener("click", function(){
    panelLateral.style.right = '-400px';
    abrirFormulario.style.display = 'none';
});

// Abre el Formulario al hacer click en el botón 'boton-bloque-formulario'
abrirFormulario.addEventListener("click",function(){
    formulario.style.display='flex';
    abrirFormulario.style.display='none';
})

// Cierra el Formulario al hacer click en el botón 'cancelar-comentario' y vuelve a activar el botón de añadir comentario
cerrarFormulario.addEventListener("click",function(){
    formulario.style.display='none';
    inputNombre.value = '';
    inputEmail.value = '';
    inputTexto.value = '';
    abrirFormulario.style.display='flex';
})



// Validación del formulario antes de enviarlo al servidor
formulario.addEventListener("submit", function(event) {
    
    // Obtenemos los valores limpiando los espacios en blanco de los extremos
    const valorNombre = inputNombre.value.trim();
    const valorEmail = inputEmail.value.trim();
    const valorTexto = inputTexto.value.trim();

    // 1. Validación de campos vacíos
    if (valorNombre === '' || valorEmail === '' || valorTexto === '') {
        event.preventDefault(); // FRENAMOS EL ENVÍO AL SERVIDOR PORQUE HAY UN ERROR
        alert("Debe rellenar obligatoriamente los campos del formulario");
        return;
    }

    // 2. Validación de formato de email
    const emailRegex = /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(valorEmail)) {
        event.preventDefault(); // FRENAMOS EL ENVÍO AL SERVIDOR PORQUE HAY UN ERROR
        alert("Por favor, introduce un correo electrónico válido (ejemplo: usuario@dominio.com)");
        return;
    }

    // 3. Envío al Backend
    // IMPORTANTE: Hemos borrado toda la manipulación del DOM (crear <li>, inyectar HTML, etc.).
    // Al NO ejecutar event.preventDefault() en este punto de éxito, el navegador continuará 
    // con su comportamiento nativo: empaquetar los inputs y enviarlos por el método POST 
    // hacia la URL definida en el action del <form> (tu archivo noticia.php).
});

// Si el campo inputTexto escucha un texto que coincida con alguno de la lista inicial "localidades" lo pone en mayúscula y lo reemplaza 
inputTexto.addEventListener("input", function() {
    let valorTexto = inputTexto.value;
    localidades.forEach(pueblo => {
        const regex = new RegExp(`\\b${pueblo}\\b`, 'gi');
        valorTexto = valorTexto.replace(regex, pueblo.toUpperCase());
    });

    inputTexto.value = valorTexto;
});


/*
\\b Significa "límite de palabra".
${pueblo}: Aquí inyectamos el nombre del pueblo que toca revisar en el bucle.
'g' que busque todas las veces que aparezca esa palabra en el texto, no solo la primera.
'i' (Case Insensitive).
 */