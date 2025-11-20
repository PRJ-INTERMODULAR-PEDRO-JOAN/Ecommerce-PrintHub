document.addEventListener("DOMContentLoaded", () => {
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

    const apiUrl = `http://172.16.221.74:3000/${tipo}?id=${id}`;
    
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
});
