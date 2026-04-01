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



// Mostrar con el resto de comentarios el comentario escrito
// Escuchamos el evento 'submit' (enviar) directamente en el formulario
formulario.addEventListener("submit", function(event) {
    // Frenamos en seco al navegador para que no recargue la página
    event.preventDefault(); 
    
    // Sacamos el valor (.value) de lo que el usuario ha escrito
    const valorNombre = inputNombre.value;
    const valorEmail = inputEmail.value;
    const valorTexto = inputTexto.value;

    // Si hay alguno de los tres campos vacíos salta un alert avisando que se deben rellenar
    if((valorNombre=='')||(valorEmail=='')||(valorTexto=='')){
        alert("Debe rellenar obligatoriamente los campos del formulario");
        return;
    }

    // Expresión regular para validar que el email introducido es válido
    const emailRegex = /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(valorEmail)) {
        alert("Por favor, introduce un correo electrónico válido (ejemplo: usuario@dominio.com)");
        return;
    }

    // Selecciona la lista HTML donde van los comentarios
    const lista = document.querySelector('.comments');

    // Crea la fecha actual
    const ahora = new Date();
    // Obtener la hora y minutos (asegurando dos dígitos para los minutos)
    const hora = ahora.getHours();
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    // Obtener la fecha en formato local (DD/MM/YYYY)
    const fecha = ahora.toLocaleDateString('es-ES');
    const fechaHoy = `${hora}:${minutos} | ${fecha}`;

    // Construimos el HTML del nuevo comentario mezclando código y variables
    // Usamos ( ` ) para poder meter variables dentro con ${ }
    const nuevoHTML = `
        <li> 
            <div class="c-nombre"> ${valorNombre} </div> 
            <div class="c-hora"> ${fechaHoy} </div>
            <div class="c-comentario"> ${valorTexto} </div>
        </li>
    `;

    // Lo inyectamos al inicio de la lista
    // innerHTML saca el HTML actual de la lista. Le sumamos el nuevo delante.
    lista.innerHTML = nuevoHTML + lista.innerHTML;

    // Ocultamos el formulario y limpiamos los campos
    formulario.style.display = 'none';
    inputNombre.value = '';
    inputEmail.value = '';
    inputTexto.value = '';
    abrirFormulario.style.display = 'flex';

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