// ── Panel de comentarios ─────────────────────────────────────────────────────

const panelLateral      = document.querySelector('.formulario');
const zonaActiva        = document.getElementById('zona-derecha');
const pestanaAbrir      = document.getElementById('pestana-abrir');
const cerrar            = document.getElementById('cancel');
const abrirFormulario   = document.getElementById('boton-bloque-formulario');
const formulario        = document.querySelector('.bloque-formulario');
const cerrarFormulario  = document.getElementById('cancelar-comentario');
const inputNombre       = document.getElementById('autor-comentario');
const inputEmail        = document.getElementById('email-comentario');
const inputTexto        = document.getElementById('texto-comentario');

if (zonaActiva) {
    zonaActiva.addEventListener('mouseenter', function () {
        panelLateral.style.right = '0';
        abrirFormulario.style.display = 'flex';
    });
}

if (pestanaAbrir) {
    pestanaAbrir.addEventListener('click', function () {
        panelLateral.style.right = '0';
        abrirFormulario.style.display = 'flex';
    });
}

if (cerrar) {
    cerrar.addEventListener('click', function () {
        panelLateral.style.right = '-400px';
        abrirFormulario.style.display = 'none';
    });
}

if (abrirFormulario) {
    abrirFormulario.addEventListener('click', function () {
        formulario.style.display = 'flex';
        abrirFormulario.style.display = 'none';
    });
}

if (cerrarFormulario) {
    cerrarFormulario.addEventListener('click', function () {
        formulario.style.display = 'none';
        inputNombre.value = '';
        inputEmail.value  = '';
        inputTexto.value  = '';
        abrirFormulario.style.display = 'flex';
    });
}

if (formulario) {
    formulario.addEventListener('submit', function (event) {
        const valorNombre = inputNombre.value.trim();
        const valorEmail  = inputEmail.value.trim();
        const valorTexto  = inputTexto.value.trim();

        if (valorNombre === '' || valorEmail === '' || valorTexto === '') {
            event.preventDefault();
            alert('Debe rellenar obligatoriamente los campos del formulario');
            return;
        }

        const emailRegex = /^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(valorEmail)) {
            event.preventDefault();
            alert('Por favor, introduce un correo electrónico válido (ejemplo: usuario@dominio.com)');
            return;
        }
    });
}

// ── Resaltado de localidades en el texto del comentario ──────────────────────
// Los datos vienen del atributo data-localidades del <main>, puesto desde Twig
// sin necesidad de ningún <script> inline

const mainNoticia = document.querySelector('.main-noticia');
const localidades = mainNoticia
    ? JSON.parse(mainNoticia.dataset.localidades || '[]')
    : [];

if (inputTexto) {
    inputTexto.addEventListener('input', function () {
        let valorTexto = inputTexto.value;
        localidades.forEach(function (pueblo) {
            const regex = new RegExp('\\b' + pueblo + '\\b', 'gi');
            valorTexto = valorTexto.replace(regex, pueblo.toUpperCase());
        });
        inputTexto.value = valorTexto;
    });
}

// ── Edición inline de comentarios desde la página de noticia ─────────────────

function toggleEditarComentario(id) {
    const form  = document.getElementById('form-editar-' + id);
    const texto = document.getElementById('texto-' + id);
    if (!form || !texto) return;
    const oculto = form.style.display === 'none';
    form.style.display  = oculto ? 'block' : 'none';
    texto.style.display = oculto ? 'none'  : 'block';
}

// ── Edición inline de comentarios desde el panel de gestión ──────────────────

function toggleEditar(id) {
    const fila = document.getElementById('fila-editar-' + id);
    if (!fila) return;
    fila.style.display = fila.style.display === 'none' ? 'table-row' : 'none';
}

// ── Añadir hashtags dinámicamente (formulario de noticia) ────────────────────

const btnAddTag = document.getElementById('btn-add-tag');
if (btnAddTag) {
    btnAddTag.addEventListener('click', function () {
        const input     = document.getElementById('nuevo-tag-input');
        const nombre    = input.value.trim().toLowerCase().replace(/\s+/g, '');
        const container = document.getElementById('nuevos-tags-container');

        if (nombre === '') return;

        const existentes = container.querySelectorAll('input[type=checkbox]');
        for (const cb of existentes) {
            if (cb.value === nombre) { input.value = ''; return; }
        }

        const label = document.createElement('label');
        label.className = 'hashtag-opcion hashtag-nuevo';
        label.innerHTML = '<input type="checkbox" name="hashtags[]" value="' + nombre + '" checked> #' + nombre;
        container.appendChild(label);
        input.value = '';
    });
}

// ── Alerta de errores del servidor (páginas de gestión) ──────────────────────
// El error se pasa como <meta name="server-error"> en el <head> del Twig
// sin ningún <script> inline

const metaError = document.querySelector('meta[name="server-error"]');
if (metaError) {
    alert(metaError.getAttribute('content'));
}