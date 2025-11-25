// src/js/productDetail.js
import { 
    getDBItemDetail, getDBComments, addDBComment, updateDBComment, 
    removeDBComment, getDBLikes, checkDBUserLike, addDBLike, 
    removeDBLike, getDBUser 
} from './api.js';

document.addEventListener("DOMContentLoaded", async () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    const tipo = params.get('tipo');
    const contenedor = document.getElementById("detalle-contenedor");

    if (!id || !tipo) {
        contenedor.innerHTML = `<h2>⚠ Error</h2>`;
        return;
    }

    // 1. CARGA DEL PRODUCTO
    const item = await getDBItemDetail(tipo, id);
    if (!item) {
        contenedor.innerHTML = `<h2>Producto no encontrado</h2>`;
        return;
    }

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

    // 2. SISTEMA DE COMENTARIOS Y LIKES
    initCommentsAndLikes(id);
});

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function initCommentsAndLikes(productId) {
    const commentsList = document.getElementById('commentsList');
    const formComment = document.getElementById('formComment');
    const btnLike = document.getElementById('btnLikeProduct');
    const formContainer = document.getElementById('commentFormContainer');
    const loginWarning = document.getElementById('loginWarning');

    // --- VALIDACIÓN DE SESIÓN (COMO ANTES) ---
    // Comprobamos si existe la cookie de sesión de PHP
    const isLogged = document.cookie.includes('user_id');
    
    // Intentamos obtener el ID de usuario de la cookie (si tu PHP la pone)
    // Si no hay cookie user_id pero está logueado, asumimos ID 1 para que no falle el json-server.
    let currentUserId = getCookie('user_id'); 
    if (isLogged && !currentUserId) currentUserId = 1; 

    if (isLogged) {
        if (formContainer) formContainer.style.display = 'block';
        if (loginWarning) loginWarning.style.display = 'none';
    } else {
        if (formContainer) formContainer.style.display = 'none';
        if (loginWarning) loginWarning.style.display = 'block';
    }

    let isUserEditing = false;

    // --- CARGAR COMENTARIOS ---
    async function loadComments() {
        if (isUserEditing) return;

        try {
            const rawComments = await getDBComments(productId);
            
            // Enriquecer con nombres de usuario
            const comments = await Promise.all(rawComments.map(async c => {
                const user = await getDBUser(c.user_id);
                return {
                    ...c,
                    author: user.nom_usuari || "Usuario",
                    // Solo permitimos editar si está logueado Y es el dueño
                    can_edit: (isLogged && c.user_id == currentUserId),
                    can_delete: (isLogged && c.user_id == currentUserId)
                };
            }));

            // Calcular estrellas
            const avg = comments.length 
                ? (comments.reduce((a, c) => a + Number(c.rating), 0) / comments.length).toFixed(1) 
                : 0;
            
            const ratingDisplay = document.getElementById('avgRatingDisplay');
            const starsDisplay = document.getElementById('avgStarsDisplay');
            if (ratingDisplay) ratingDisplay.innerText = avg;
            if (starsDisplay) starsDisplay.innerHTML = renderStars(avg);

            commentsList.innerHTML = '';
            if (comments.length === 0) {
                commentsList.innerHTML = '<div class="text-center p-4 text-muted"><p>Aún no hay opiniones.</p></div>';
                return;
            }

            comments.forEach(c => {
                let actions = '';
                if (c.can_edit) actions += `<button class="btn btn-link p-0 me-2 text-primary btn-edit" data-id="${c.id}" data-text="${c.text}" data-rating="${c.rating}"><i class="fas fa-pen"></i></button>`;
                if (c.can_delete) actions += `<button class="btn btn-link p-0 text-danger btn-delete" data-id="${c.id}"><i class="fas fa-trash"></i></button>`;

                commentsList.innerHTML += `
                    <div class="card mb-3 border-0 shadow-sm">
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
                                        <input id="edit-rating-${c.id}" type="number" min="1" max="5" class="form-control mb-2" style="width:60px;">
                                        <textarea id="edit-text-${c.id}" class="form-control mb-2"></textarea>
                                        <button class="btn btn-sm btn-secondary btn-cancel" data-id="${c.id}">Cancelar</button>
                                        <button class="btn btn-sm btn-success btn-save" data-id="${c.id}">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
            attachEvents();
        } catch (err) { console.error(err); }
    }

    function attachEvents() {
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                isUserEditing = true;
                const id = btn.dataset.id;
                document.getElementById(`view-mode-${id}`).style.display = 'none';
                document.getElementById(`edit-mode-${id}`).style.display = 'block';
                document.getElementById(`edit-text-${id}`).value = btn.dataset.text;
                document.getElementById(`edit-rating-${id}`).value = btn.dataset.rating;
            });
        });
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                document.getElementById(`edit-mode-${id}`).style.display = 'none';
                document.getElementById(`view-mode-${id}`).style.display = 'block';
                isUserEditing = false;
            });
        });
        document.querySelectorAll('.btn-save').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                const text = document.getElementById(`edit-text-${id}`).value;
                const rating = document.getElementById(`edit-rating-${id}`).value;
                await updateDBComment(id, { text, rating: Number(rating) });
                isUserEditing = false;
                loadComments();
            });
        });
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirm('¿Borrar?')) {
                    await removeDBComment(btn.dataset.id);
                    loadComments();
                }
            });
        });
    }

    // --- ENVIAR COMENTARIO ---
    if (formComment) {
        formComment.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!isLogged) return; // Seguridad extra

            const text = document.getElementById('inputText').value;
            const rating = document.getElementById('inputRating').value;
            const date = new Date().toISOString().slice(0, 19).replace('T', ' ');

            await addDBComment({ 
                product_id: productId, 
                user_id: parseInt(currentUserId), 
                text, 
                rating: Number(rating), 
                date 
            });
            formComment.reset();
            loadComments();
        });
    }

    // --- LIKES ---
    async function loadLikes() {
        const likes = await getDBLikes(productId);
        document.getElementById('likeCount').innerText = likes.length;

        if (!isLogged) return; // Si no está logueado, no comprobamos su like

        const myLike = await checkDBUserLike(productId, currentUserId);
        const icon = btnLike.querySelector('i');
        
        if (myLike) {
            btnLike.classList.replace('btn-outline-danger', 'btn-danger');
            icon.className = 'fas fa-heart me-2';
            btnLike.dataset.likeId = myLike.id;
        } else {
            btnLike.classList.replace('btn-danger', 'btn-outline-danger');
            icon.className = 'far fa-heart me-2';
            delete btnLike.dataset.likeId;
        }
    }

    if (btnLike) {
        btnLike.addEventListener('click', async () => {
            // AQUÍ ESTÁ LA REDIRECCIÓN QUE PEDÍAS
            if (!isLogged) {
                window.location.href = '../auth/login.php';
                return;
            }

            const likeId = btnLike.dataset.likeId;
            if (likeId) {
                await removeDBLike(likeId);
            } else {
                await addDBLike({ product_id: productId, user_id: parseInt(currentUserId) });
            }
            loadLikes();
        });
    }

    function renderStars(r) {
        let h = ''; for (let i = 1; i <= 5; i++) h += i <= r ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
        return h;
    }

    loadComments();
    loadLikes();
    setInterval(loadComments, 2000);
    setInterval(loadLikes, 5000);
}