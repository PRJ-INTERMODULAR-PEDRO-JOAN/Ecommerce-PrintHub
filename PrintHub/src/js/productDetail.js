document.addEventListener("DOMContentLoaded", () => {
    // ---------------------------------------------------------
    // 1. TU CÓDIGO ORIGINAL (CARGA DEL PRODUCTO) - NO TOCADO
    // ---------------------------------------------------------
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    const tipo = params.get('tipo');

    const contenedor = document.getElementById("detalle-contenedor");

    if (!id || !tipo) {
        contenedor.innerHTML = `
            <div class="mensaje-centro">
                <h2>⚠ Error de parámetros</h2>
                <p>No sabemos qué producto buscar. Vuelve al inicio.</p>
                <a href="../index.html" class="btn-volver">Volver</a>
            </div>`;
        return;
    }

    const apiUrl = `http://localhost:3000/${tipo}?id=${id}`;

    console.log("Consultando:", apiUrl);

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Datos recibidos:", data);

            const item = Array.isArray(data) && data.length > 0 ? data[0] : null;

            if (!item) {
                throw new Error("El producto no existe en la lista recibida.");
            }

            let imgPath = item.img;
            if (imgPath && !imgPath.startsWith("http") && !imgPath.startsWith("../")) {
                imgPath = "../" + imgPath;
            }

            contenedor.innerHTML = `
                <div class="imagen-wrapper">
                    <img src="${imgPath}" alt="${item.nom}" onerror="this.src='../public/marcaDeAgua.png'">
                </div>
                <div class="info-wrapper">
                    <h1 class="info-titulo">${item.nom}</h1>
                    <p class="info-precio">${parseFloat(item.preu).toFixed(2)} €</p>
                    <p class="stock-info">Disponible: ${item.estoc} unidades</p>
                    
                    <h3 class="info-descripcion-titulo">Descripción</h3>
                    <p class="info-descripcion">${item.descripcio}</p>
                    
                    <button class="btn-comprar" onclick="alert('¡${item.nom} añadido al carrito!')">
                        Añadir al Carrito
                    </button>
                </div>
            `;
        })
        .catch(error => {
            console.error("Error fetch:", error);
            contenedor.innerHTML = `
                <div class="mensaje-centro">
                    <h2>No se pudo cargar el producto</h2>
                    <p>Hubo un problema conectando con el servidor de datos.</p>
                    <small style="color: red; background: #fff0f0; padding: 5px; border-radius: 5px;">
                        ${error.message} <br> URL: ${apiUrl}
                    </small>
                    <br><br>
                    <a href="../index.html" class="btn-volver">Volver a la tienda</a>
                </div>
            `;
        });

    // ---------------------------------------------------------
    // 2. NUEVA LÓGICA (COMENTARIOS Y LIKES)
    // ---------------------------------------------------------

    // Iniciamos el sistema de comentarios pasando el ID del producto
    initCommentsAndLikes(id);
});

/**
 * Función que encapsula toda la lógica de comentarios y likes
 * para no interferir con la carga del producto.
 */
function initCommentsAndLikes(productId) {
    if (!productId) return;

    // Selectores del DOM (Asegúrate de haber actualizado el HTML)
    const commentsList = document.getElementById('commentsList');
    const formComment = document.getElementById('formComment');
    const btnLike = document.getElementById('btnLikeProduct');
    const formContainer = document.getElementById('commentFormContainer');
    const loginWarning = document.getElementById('loginWarning');

    // Si no existen los elementos en el HTML (por si acaso no copiaste el HTML nuevo), salimos
    if (!commentsList || !btnLike) return;

    // 1. Detectar si el usuario está logueado (Buscando cookie de sesión PHP)
    const isLogged = document.cookie.split(';').some((item) => item.trim().startsWith('PHPSESSID='));

    if (isLogged) {
        if (formContainer) formContainer.style.display = 'block';
        if (loginWarning) loginWarning.style.display = 'none';
    } else {
        if (formContainer) formContainer.style.display = 'none';
        if (loginWarning) loginWarning.style.display = 'block';
    }

    // 2. Función para Cargar Comentarios
    async function loadComments() {
        try {
            // Llamada a TU api PHP
            const res = await fetch(`../api/comments.php?product_id=${productId}`);
            const data = await res.json();

            // Actualizar estadísticas
            const ratingDisplay = document.getElementById('avgRatingDisplay');
            const starsDisplay = document.getElementById('avgStarsDisplay');

            if (ratingDisplay) ratingDisplay.innerText = data.avg_rating;
            if (starsDisplay) starsDisplay.innerHTML = renderStars(data.avg_rating);

            // Actualizar lista
            commentsList.innerHTML = '';
            if (!data.comments || data.comments.length === 0) {
                commentsList.innerHTML = '<p class="text-muted text-center">Aún no hay comentarios. ¡Sé el primero!</p>';
                return;
            }

            data.comments.forEach(c => {
                // Botón borrar solo si es dueño o admin
                const deleteBtn = (c.is_owner || c.is_admin)
                    ? `<button class="btn btn-sm btn-link text-danger btn-delete" data-id="${c.id}" style="float:right;">Eliminar</button>`
                    : '';

                commentsList.innerHTML += `
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-1" style="margin:0;">${c.author}</h6>
                            <small class="text-muted">${c.date}</small>
                        </div>
                        <div class="text-warning mb-2" style="color: #ffc107;">${renderStars(c.rating)}</div>
                        <p class="mb-1">${c.text}</p>
                        ${deleteBtn}
                    </div>
                `;
            });

            // Asignar eventos a los botones de borrar generados dinámicamente
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', (e) => deleteComment(e.target.dataset.id));
            });

        } catch (error) {
            console.error("Error cargando comentarios:", error);
        }
    }

    // 3. Función para Cargar Likes
    async function loadLikes() {
        try {
            const res = await fetch('../api/likes.php', {
                method: 'POST',
                body: JSON.stringify({ product_id: productId, action: 'get' })
            });
            const data = await res.json();

            const countSpan = document.getElementById('likeCount');
            if (countSpan) countSpan.innerText = data.count;

            const icon = btnLike.querySelector('i');

            if (data.liked) {
                btnLike.classList.remove('btn-outline-danger');
                btnLike.classList.add('btn-danger');
                if (icon) {
                    icon.classList.remove('far'); // Corazón vacío
                    icon.classList.add('fas');    // Corazón lleno
                }
            } else {
                btnLike.classList.add('btn-outline-danger');
                btnLike.classList.remove('btn-danger');
                if (icon) {
                    icon.classList.add('far');
                    icon.classList.remove('fas');
                }
            }
        } catch (err) { console.error("Error likes:", err); }
    }

    // 4. Evento: Enviar Comentario
    if (formComment) {
        formComment.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = document.getElementById('inputText').value;
            const rating = document.getElementById('inputRating').value;

            try {
                const res = await fetch('../api/comments.php?action=add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId, text, rating })
                });
                const json = await res.json();

                if (json.success) {
                    formComment.reset();
                    loadComments(); // Recargar lista
                } else {
                    alert(json.error || "Error al publicar. ¿Estás logueado?");
                }
            } catch (err) { console.error(err); }
        });
    }

    // 5. Función: Borrar Comentario
    async function deleteComment(id) {
        if (!confirm('¿Seguro que quieres borrar este comentario?')) return;
        try {
            const res = await fetch('../api/comments.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ comment_id: id })
            });
            if (res.ok) loadComments();
        } catch (err) { console.error(err); }
    }

    // 6. Evento: Toggle Like
    btnLike.addEventListener('click', async () => {
        try {
            const res = await fetch('../api/likes.php', {
                method: 'POST',
                body: JSON.stringify({ product_id: productId })
            });
            const json = await res.json();

            if (json.error === 'Unauthenticated') {
                // Redirigir a login si no está logueado
                window.location.href = '../auth/login.php';
            } else if (json.success) {
                loadLikes(); // Refrescar contador
            }
        } catch (err) { console.error(err); }
    });

    // Helper para pintar estrellas
    function renderStars(rating) {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) html += '<i class="fas fa-star"></i>';
            else if (i - 0.5 <= rating) html += '<i class="fas fa-star-half-alt"></i>';
            else html += '<i class="far fa-star"></i>';
        }
        return html;
    }

    // Iniciar carga inicial
    loadComments();
    loadLikes();
}