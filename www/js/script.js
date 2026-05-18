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

// ── Resaltado de localidades ──────────────────────────────────────────────────

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

// ── Confirmación antes de enviar formularios peligrosos ──────────────────────
// Todos los <form class="form-confirmar" data-mensaje="..."> piden confirm()

document.querySelectorAll('.form-confirmar').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        const mensaje = form.dataset.mensaje || '¿Estás seguro?';
        if (!confirm(mensaje)) {
            e.preventDefault();
        }
    });
});

// ── Edición inline de comentarios desde la página de noticia ─────────────────
// Botones con clase btn-toggle-comentario y data-id

document.querySelectorAll('.btn-toggle-comentario').forEach(function (btn) {
    btn.addEventListener('click', function () {
        toggleEditarComentario(btn.dataset.id);
    });
});

// Botones cancelar con clase btn-cancelar-comentario y data-id
document.querySelectorAll('.btn-cancelar-comentario').forEach(function (btn) {
    btn.addEventListener('click', function () {
        toggleEditarComentario(btn.dataset.id);
    });
});

function toggleEditarComentario(id) {
    const form  = document.getElementById('form-editar-' + id);
    const texto = document.getElementById('texto-' + id);
    if (!form || !texto) return;
    const oculto = form.style.display === 'none';
    form.style.display  = oculto ? 'block' : 'none';
    texto.style.display = oculto ? 'none'  : 'block';
}

// ── Edición inline de comentarios desde el panel de gestión ──────────────────
// Botones con clase btn-toggle-gestion y data-id

document.querySelectorAll('.btn-toggle-gestion').forEach(function (btn) {
    btn.addEventListener('click', function () {
        toggleEditar(btn.dataset.id);
    });
});

document.querySelectorAll('.btn-cancelar-gestion').forEach(function (btn) {
    btn.addEventListener('click', function () {
        toggleEditar(btn.dataset.id);
    });
});

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

// ── Alerta de errores del servidor ───────────────────────────────────────────

const metaError = document.querySelector('meta[name="server-error"]');
if (metaError) {
    alert(metaError.getAttribute('content'));
}