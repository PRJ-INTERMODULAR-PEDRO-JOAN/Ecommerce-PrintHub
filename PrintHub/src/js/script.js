// src/js/script.js
import { getDBProducts, getDBPrinters } from './api.js';

// Mensaje botones productos
document.querySelectorAll('.boton').forEach(button => {
  button.addEventListener('click', () => {
    alert('Estàs veient més informació del producte!');
  });
});

document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM cargado");
  // Verificamos si existen los contenedores antes de cargar para evitar errores en otras páginas
  if(document.getElementById("contenedor-productos")) {
      cargarProductos();
  }
  if(document.getElementById("contenedor-impresoras")) {
      cargarImpresoras();
  }
});

function cargarProductos() {
  console.log("Cargando productos con api.js...");

  getDBProducts()
    .then(data => {
      console.log("Datos recibidos:", data);
      const contenedor = document.getElementById("contenedor-productos");
      contenedor.innerHTML = ''; // Limpiar antes de pintar

      data.forEach(producto => {
        const card = document.createElement("div");
        card.classList.add("tarjeta-producto");

        // Corrección ruta imagen para que se vea en local
        let imgPath = producto.img;
        if (imgPath && !imgPath.startsWith("http") && !imgPath.startsWith("public/")) {
             // Asumimos que si no tiene http ni public, le falta public/
             imgPath = "public/" + imgPath; 
        }

        card.innerHTML = `
                  <img src="${imgPath}" alt="${producto.nom}" onerror="this.src='public/marcaDeAgua.png'">
                  <h2>${producto.nom}</h2>
                  <p class="producto-descripcion">${producto.descripcio}</p>
                  <span class="producto-precio">${producto.preu.toFixed(2)} €</span>
                  <a href="src/productDetail.php?id=${producto.id}&tipo=productes" class="boton">Ver Detalles</a>              
                `;

        contenedor.appendChild(card);
      });
    })
    .catch(err => console.error("Error cargando productos:", err));
}

function cargarImpresoras() {
  console.log("Cargando impresoras con api.js...");

  getDBPrinters()
    .then(data => {
      console.log("Datos recibidos:", data);
      const contenedor = document.getElementById("contenedor-impresoras");
      contenedor.innerHTML = '';

      data.forEach(impresora => {
        const card = document.createElement("div");
        card.classList.add("tarjeta-producto");

        let imgPath = impresora.img;
        if (imgPath && !imgPath.startsWith("http") && !imgPath.startsWith("public/")) {
             imgPath = "public/" + imgPath; 
        }

        card.innerHTML = `
                  <img src="${imgPath}" alt="${impresora.nom}" onerror="this.src='public/marcaDeAgua.png'">
                  <h2>${impresora.nom}</h2>
                  <p class="producto-descripcion">${impresora.descripcio}</p>
                  <span class="producto-precio">${impresora.preu.toFixed(2)} €</span>
                  <a href="src/productDetail.php?id=${impresora.id}&tipo=impresoras" class="boton">Ver Detalles</a>       
                `;

        contenedor.appendChild(card);
      });
    })
    .catch(err => console.error("Error cargando impresoras:", err));
}