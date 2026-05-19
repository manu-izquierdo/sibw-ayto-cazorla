// ── Panel de comentarios ─────────────────────────────────────────────────────

const panelLateral     = document.querySelector('.formulario');
const zonaActiva       = document.getElementById('zona-derecha');
const pestanaAbrir     = document.getElementById('pestana-abrir');
const cerrar           = document.getElementById('cancel');
const abrirFormulario  = document.getElementById('boton-bloque-formulario');
const formulario       = document.querySelector('.bloque-formulario');
const cerrarFormulario = document.getElementById('cancelar-comentario');
const inputNombre      = document.getElementById('autor-comentario');
const inputEmail       = document.getElementById('email-comentario');
const inputTexto       = document.getElementById('texto-comentario');

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

// ── Confirmación antes de borrar ─────────────────────────────────────────────
// Usamos delegación en document para que funcione también con filas
// reconstruidas por AJAX (si usáramos querySelectorAll, los nuevos
// elementos no tendrían el listener)

document.addEventListener('submit', function (e) {
    if (e.target.classList.contains('form-confirmar')) {
        const mensaje = e.target.dataset.mensaje || '¿Estás seguro?';
        if (!confirm(mensaje)) {
            e.preventDefault();
        }
    }
});

// ── Edición inline de comentarios en noticia ──────────────────────────────────

document.querySelectorAll('.btn-toggle-comentario').forEach(function (btn) {
    btn.addEventListener('click', function () { toggleEditarComentario(btn.dataset.id); });
});

document.querySelectorAll('.btn-cancelar-comentario').forEach(function (btn) {
    btn.addEventListener('click', function () { toggleEditarComentario(btn.dataset.id); });
});

function toggleEditarComentario(id) {
    const form  = document.getElementById('form-editar-' + id);
    const texto = document.getElementById('texto-' + id);
    if (!form || !texto) return;
    const oculto = form.style.display === 'none';
    form.style.display  = oculto ? 'block' : 'none';
    texto.style.display = oculto ? 'none'  : 'block';
}

// ── Edición inline de comentarios en gestión ─────────────────────────────────

document.querySelectorAll('.btn-toggle-gestion').forEach(function (btn) {
    btn.addEventListener('click', function () { toggleEditar(btn.dataset.id); });
});

document.querySelectorAll('.btn-cancelar-gestion').forEach(function (btn) {
    btn.addEventListener('click', function () { toggleEditar(btn.dataset.id); });
});

function toggleEditar(id) {
    const fila = document.getElementById('fila-editar-' + id);
    if (!fila) return;
    fila.style.display = fila.style.display === 'none' ? 'table-row' : 'none';
}

// ── Añadir hashtags dinámicamente ─────────────────────────────────────────────

const btnAddTag = document.getElementById('btn-add-tag');
if (btnAddTag) {
    btnAddTag.addEventListener('click', function () {
        const input     = document.getElementById('nuevo-tag-input');
        const nombre    = input.value.trim().toLowerCase().replace(/\s+/g, '');
        const container = document.getElementById('nuevos-tags-container');

        if (nombre === '') return;

        for (const cb of container.querySelectorAll('input[type=checkbox]')) {
            if (cb.value === nombre) { input.value = ''; return; }
        }

        const label = document.createElement('label');
        label.className = 'hashtag-opcion hashtag-nuevo';
        label.innerHTML = '<input type="checkbox" name="hashtags[]" value="' + nombre + '" checked> #' + nombre;
        container.appendChild(label);
        input.value = '';
    });
}

// ── Alerta de errores del servidor ────────────────────────────────────────────

const metaError = document.querySelector('meta[name="server-error"]');
if (metaError) {
    alert(metaError.getAttribute('content'));
}


// =============================================================================
// P5 – AJAX
// =============================================================================

// ── Búsqueda dinámica en portada (desplegable) ────────────────────────────────

var inputPortada    = document.getElementById('busqueda-portada');
var dropdownPortada = document.getElementById('dropdown-portada');

if (inputPortada) {

    inputPortada.addEventListener('input', async function () {
        var q = this.value.trim();

        if (q.length < 1) {
            dropdownPortada.innerHTML = '';
            dropdownPortada.style.display = 'none';
            return;
        }

        var url = '/ajax/buscar?tipo=portada&q=' + encodeURIComponent(q);
        const peticion = await fetch(url);
        const noticias = await peticion.json();

        if (noticias.length === 0) {
            dropdownPortada.innerHTML = '';
            dropdownPortada.style.display = 'none';
            return;
        }

        var html = '';
        noticias.forEach(function (n) {
            html += '<a href="/noticia/' + n.id + '" class="dropdown-item">' + n.titulo + '</a>';
        });
        dropdownPortada.innerHTML = html;
        dropdownPortada.style.display = 'block';
    });

    // Cierra el desplegable al hacer clic fuera
    document.addEventListener('click', function (e) {
        if (e.target !== inputPortada && !dropdownPortada.contains(e.target)) {
            dropdownPortada.style.display = 'none';
        }
    });
}

// ── Búsqueda AJAX en gestión de noticias ─────────────────────────────────────

var formBusquedaNoticias = document.getElementById('form-busqueda-noticias');
var tbodyNoticias        = document.getElementById('tbody-noticias');

// Construye una fila <tr> a partir de un objeto noticia recibido en JSON
function construirFilaNoticia(n) {
    var img    = n.ruta
        ? '<img src="' + n.ruta + '" class="thumb-noticia">'
        : '<span class="sin-imagen">Sin imagen</span>';
    var checked = n.publicado == 1 ? 'checked' : '';
    var partes  = n.fecha ? n.fecha.substring(0, 10).split('-') : ['', '', ''];
    var fecha   = partes[2] + '/' + partes[1] + '/' + partes[0];

    return '<tr>'
         + '<td>' + n.id + '</td>'
         + '<td>' + img + '</td>'
         + '<td><a href="/noticia/' + n.id + '" class="enlace-noticia">' + n.titulo + '</a></td>'
         + '<td>' + (n.tipo || '') + '</td>'
         + '<td>' + (n.concejalia || '') + '</td>'
         + '<td>' + fecha + '</td>'
         + '<td><input type="checkbox" class="checkbox-publicado" data-id="' + n.id + '" ' + checked + '></td>'
         + '<td class="td-acciones">'
         +   '<a href="/noticias?accion=form-editar&id=' + n.id + '" class="btn-icono btn-editar-toggle">'
         +     '<i class="fa-solid fa-pen"></i>'
         +   '</a>'
         +   '<form action="/noticias" method="POST" style="display:inline"'
         +         ' class="form-confirmar" data-mensaje="¿Borrar esta noticia?">'
         +     '<input type="hidden" name="accion" value="borrar">'
         +     '<input type="hidden" name="id_noticia" value="' + n.id + '">'
         +     '<button type="submit" class="btn-icono btn-borrar">'
         +       '<i class="fa-solid fa-trash"></i>'
         +     '</button>'
         +   '</form>'
         + '</td>'
         + '</tr>';
}

async function buscarNoticiasGestion() {
    var titulo  = document.getElementById('buscar_titulo')  ? document.getElementById('buscar_titulo').value  : '';
    var desc    = document.getElementById('buscar_desc')    ? document.getElementById('buscar_desc').value    : '';
    var hashtag = document.getElementById('buscar_hashtag') ? document.getElementById('buscar_hashtag').value : '';

    var url = '/ajax/buscar?tipo=gestion'
        + '&buscar_titulo='  + encodeURIComponent(titulo)
        + '&buscar_desc='    + encodeURIComponent(desc)
        + '&buscar_hashtag=' + encodeURIComponent(hashtag);

    const peticion = await fetch(url);
    const noticias = await peticion.json();

    if (!tbodyNoticias) return;

    if (noticias.length === 0) {
        tbodyNoticias.innerHTML = '<tr><td colspan="8" class="gestion-vacio">No se encontraron noticias.</td></tr>';
        return;
    }

    var html = '';
    noticias.forEach(function (n) { html += construirFilaNoticia(n); });
    tbodyNoticias.innerHTML = html;
}

if (formBusquedaNoticias) {
    formBusquedaNoticias.addEventListener('submit', function (e) {
        e.preventDefault();
        buscarNoticiasGestion();
    });

    var btnLimpiarNoticias = formBusquedaNoticias.querySelector('.btn-limpiar-noticias');
    if (btnLimpiarNoticias) {
        btnLimpiarNoticias.addEventListener('click', function (e) {
            e.preventDefault();
            formBusquedaNoticias.reset();
            buscarNoticiasGestion();
        });
    }
}

// ── Toggle publicado con AJAX ─────────────────────────────────────────────────
// Usamos delegación en tbodyNoticias porque sus filas se reconstruyen
// con innerHTML tras cada búsqueda AJAX

if (tbodyNoticias) {
    tbodyNoticias.addEventListener('change', async function (e) {
        if (!e.target.classList.contains('checkbox-publicado')) return;

        var checkbox = e.target;
        var datos = new FormData();
        datos.append('id_noticia', checkbox.dataset.id);

        const peticion = await fetch('/ajax/toggle-publicado', { method: 'POST', body: datos });
        const resultado = await peticion.json();

        // Sincroniza el estado visual con lo que confirma la BD
        checkbox.checked = resultado.publicado === 1;
    });
}

// ── Búsqueda instantánea en gestión (igual que portada) ──────────────────────

var inputTitulo  = document.getElementById('buscar_titulo');
var inputDesc    = document.getElementById('buscar_desc');
var selectHashtag = document.getElementById('buscar_hashtag');

if (inputTitulo) {
    inputTitulo.addEventListener('input', function () {
        buscarNoticiasGestion();
    });
}

if (inputDesc) {
    inputDesc.addEventListener('input', function () {
        buscarNoticiasGestion();
    });
}

if (selectHashtag) {
    selectHashtag.addEventListener('change', function () {
        buscarNoticiasGestion();
    });
}