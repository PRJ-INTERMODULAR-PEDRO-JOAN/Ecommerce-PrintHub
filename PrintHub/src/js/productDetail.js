document.addEventListener("DOMContentLoaded", () => {
    // ---------------------------------------------------------
    // 1. CARGA DEL PRODUCTO (CÓDIGO ORIGINAL INTACTO)
    // ---------------------------------------------------------
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    const tipo = params.get('tipo');
    const contenedor = document.getElementById("detalle-contenedor");

    if (!id || !tipo) {
        contenedor.innerHTML = `<div class="mensaje-centro"><h2>⚠ Error</h2><a href="../index.html" class="btn-volver">Volver</a></div>`;
        return;
    }

    const apiUrl = `http://172.16.221.99:3000/${tipo}?id=${id}`;
    fetch(apiUrl)
        .then(r => r.json())
        .then(data => {
            const item = data[0];
            if (!item) throw new Error("Producto no encontrado");

            let imgPath = item.img;
            if (imgPath && !imgPath.startsWith("http") && !imgPath.startsWith("../")) imgPath = "../" + imgPath;

            contenedor.innerHTML = `
                <div class="imagen-wrapper"><img src="${imgPath}" onerror="this.src='../public/marcaDeAgua.png'"></div>
                <div class="info-wrapper">
                    <h1 class="info-titulo">${item.nom}</h1>
                    <p class="info-precio">${parseFloat(item.preu).toFixed(2)} €</p>
                    <p class="stock-info">Disponible: ${item.estoc}</p>
                    <h3 class="info-descripcion-titulo">Descripción</h3>
                    <p class="info-descripcion">${item.descripcio}</p>
                    <button class="btn-comprar" onclick="alert('Añadido al carrito')">Añadir al Carrito</button>
                </div>`;
        })
        .catch(e => contenedor.innerHTML = `<p class="mensaje-centro">${e.message}</p>`);

    // 2. INICIAR SISTEMA DE COMENTARIOS EN TIEMPO REAL
    initCommentsAndLikes(id);
});

function initCommentsAndLikes(productId) {
    const commentsList = document.getElementById('commentsList');
    const formComment = document.getElementById('formComment');
    const btnLike = document.getElementById('btnLikeProduct');
    const formContainer = document.getElementById('commentFormContainer');
    const loginWarning = document.getElementById('loginWarning');

    // Bandera para saber si el usuario está "ocupado" editando
    // Si es true, pausamos la actualización automática para no borrarle el texto
    let isUserEditing = false;

    // Detectar Login
    const isLogged = document.cookie.includes('PHPSESSID');
    if (isLogged) {
        if (formContainer) formContainer.style.display = 'block';
        if (loginWarning) loginWarning.style.display = 'none';
    } else {
        if (formContainer) formContainer.style.display = 'none';
        if (loginWarning) loginWarning.style.display = 'block';
    }

    // --- CARGAR COMENTARIOS (FUNCIÓN PRINCIPAL) ---
    async function loadComments() {
        // Si el usuario está editando, NO recargamos la lista para no molestar
        if (isUserEditing) return;

        try {
            const res = await fetch(`../api/comments.php?product_id=${productId}`);
            const data = await res.json();

            // Actualizar Estadísticas
            const ratingDisplay = document.getElementById('avgRatingDisplay');
            const starsDisplay = document.getElementById('avgStarsDisplay');
            if (ratingDisplay) ratingDisplay.innerText = data.avg_rating;
            if (starsDisplay) starsDisplay.innerHTML = renderStars(data.avg_rating);

            // Renderizar Lista
            // Comprobamos si el contenido ha cambiado antes de reemplazarlo a lo bruto
            // (Opcional: aquí reemplazamos siempre para asegurar consistencia)
            commentsList.innerHTML = '';

            if (!data.comments || data.comments.length === 0) {
                commentsList.innerHTML = '<div class="text-center p-4 text-muted"><i class="far fa-comment-dots fa-3x mb-3"></i><p>Aún no hay opiniones.</p></div>';
                return;
            }

            data.comments.forEach(c => {
                // Botones de acción (según permisos)
                let actions = '';
                if (c.can_edit) actions += `<button class="btn btn-link p-0 me-2 text-primary btn-edit" data-id="${c.id}" data-text="${c.text}" data-rating="${c.rating}"><i class="fas fa-pen"></i></button>`;
                if (c.can_delete) actions += `<button class="btn btn-link p-0 text-danger btn-delete" data-id="${c.id}"><i class="fas fa-trash"></i></button>`;

                commentsList.innerHTML += `
                    <div class="card mb-3 border-0 shadow-sm" id="comment-card-${c.id}">
                        <div class="card-body">
                            <div class="d-flex flex-start">
                                <div class="me-3 text-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                                        <i class="fas fa-user text-secondary fa-lg"></i>
                                    </div>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 text-primary">${c.author}</h6>
                                        <small class="text-muted">${c.date}</small>
                                    </div>
                                    
                                    <div id="view-mode-${c.id}">
                                        <div class="mb-2 text-warning small">${renderStars(c.rating)}</div>
                                        <p class="mb-2 text-dark">${c.text}</p>
                                        <div class="d-flex justify-content-end">${actions}</div>
                                    </div>

                                    <div id="edit-mode-${c.id}" style="display:none;" class="mt-2 bg-light p-3 rounded">
                                        <label class="small text-muted">Editar puntuación:</label>
                                        <select id="edit-rating-${c.id}" class="form-select form-select-sm w-auto mb-2">
                                            <option value="5">⭐⭐⭐⭐⭐</option>
                                            <option value="4">⭐⭐⭐⭐</option>
                                            <option value="3">⭐⭐⭐</option>
                                            <option value="2">⭐⭐</option>
                                            <option value="1">⭐</option>
                                        </select>
                                        <textarea id="edit-text-${c.id}" class="form-control form-control-sm mb-2" rows="2"></textarea>
                                        <div class="text-end">
                                            <button class="btn btn-sm btn-secondary btn-cancel" data-id="${c.id}">Cancelar</button>
                                            <button class="btn btn-sm btn-success btn-save" data-id="${c.id}">Guardar Cambios</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            // Reasignar eventos a los nuevos elementos HTML
            attachEvents();

        } catch (err) { console.error("Error polling:", err); }
    }

    function attachEvents() {
        // --- EDITAR ---
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                isUserEditing = true; // PAUSAR ACTUALIZACIÓN AUTOMÁTICA
                const id = btn.dataset.id;
                document.getElementById(`view-mode-${id}`).style.display = 'none';
                document.getElementById(`edit-mode-${id}`).style.display = 'block';
                document.getElementById(`edit-text-${id}`).value = btn.dataset.text;
                document.getElementById(`edit-rating-${id}`).value = btn.dataset.rating;
            });
        });

        // --- CANCELAR EDICIÓN ---
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                document.getElementById(`edit-mode-${id}`).style.display = 'none';
                document.getElementById(`view-mode-${id}`).style.display = 'block';
                isUserEditing = false; // REANUDAR ACTUALIZACIÓN
            });
        });

        // --- GUARDAR EDICIÓN ---
        document.querySelectorAll('.btn-save').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                const text = document.getElementById(`edit-text-${id}`).value;
                const rating = document.getElementById(`edit-rating-${id}`).value;

                await fetch('../api/comments.php?action=edit', {
                    method: 'POST',
                    body: JSON.stringify({ comment_id: id, text, rating })
                });
                isUserEditing = false; // Liberar bloqueo
                loadComments(); // Recarga inmediata forzada
            });
        });

        // --- BORRAR ---
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('¿Borrar comentario permanentemente?')) return;
                await fetch('../api/comments.php?action=delete', {
                    method: 'POST',
                    body: JSON.stringify({ comment_id: btn.dataset.id })
                });
                loadComments();
            });
        });
    }

    // --- ENVIAR NUEVO COMENTARIO ---
    if (formComment) {
        formComment.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = document.getElementById('inputText').value;
            const rating = document.getElementById('inputRating').value;

            const res = await fetch('../api/comments.php?action=add', {
                method: 'POST',
                body: JSON.stringify({ product_id: productId, text, rating })
            });
            const json = await res.json();

            if (json.success) {
                formComment.reset();
                loadComments(); // Cargar mi propio comentario inmediatamente
            } else {
                alert("Error: " + (json.error || "Desconocido"));
            }
        });
    }

    // --- SISTEMA DE LIKES ---
    async function loadLikes() {
        const res = await fetch('../api/likes.php', {
            method: 'POST',
            body: JSON.stringify({ product_id: productId, action: 'get' })
        });
        const data = await res.json();
        document.getElementById('likeCount').innerText = data.count;

        const btn = document.getElementById('btnLikeProduct');
        const icon = btn.querySelector('i');
        if (data.liked) {
            btn.classList.replace('btn-outline-danger', 'btn-danger');
            icon.className = 'fas fa-heart me-2';
        } else {
            btn.classList.replace('btn-danger', 'btn-outline-danger');
            icon.className = 'far fa-heart me-2';
        }
    }

    if (btnLike) {
        btnLike.addEventListener('click', async () => {
            const res = await fetch('../api/likes.php', { method: 'POST', body: JSON.stringify({ product_id: productId }) });
            const json = await res.json();
            if (json.error) window.location.href = '../auth/login.php';
            else loadLikes();
        });
    }

    function renderStars(r) {
        let h = ''; for (let i = 1; i <= 5; i++) h += i <= r ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
        return h;
    }

    // --- INICIALIZACIÓN Y TIEMPO REAL ---
    loadComments();
    loadLikes();

    // ESTO HACE LA MAGIA: Recarga comentarios cada 2 segundos
    setInterval(loadComments, 2000);
    // También recargamos Likes cada 5 segundos para ver si suben
    setInterval(loadLikes, 5000);
}